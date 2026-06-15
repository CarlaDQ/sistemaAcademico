import React, { useEffect, useState } from 'react';
import {
  View, Text, StyleSheet, TouchableOpacity, Alert, ActivityIndicator, Platform, ScrollView,
} from 'react-native';
import { Picker } from '@react-native-picker/picker';
import * as Location from 'expo-location';
import api from '../api/client';
import { useAuth } from '../auth/AuthContext';

const DIAS_LABEL = { lunes: 'Lun', martes: 'Mar', miercoles: 'Mié', jueves: 'Jue', viernes: 'Vie', sabado: 'Sáb' };

export default function AttendanceCheckinScreen() {
  const { user } = useAuth();
  const [location, setLocation] = useState(null);
  const [loading, setLoading] = useState(false);
  const [result, setResult] = useState(null);
  const [asignaciones, setAsignaciones] = useState([]);
  const [selectedAsig, setSelectedAsig] = useState(null);
  const [geocercas, setGeocercas] = useState([]);

  useEffect(() => {
    (async () => {
      const { status } = await Location.requestForegroundPermissionsAsync();
      if (status !== 'granted') { Alert.alert('Permiso denegado', 'No se puede obtener la ubicación.'); return; }
      const loc = await Location.getCurrentPositionAsync({ accuracy: Location.Accuracy.High });
      setLocation({ lat: loc.coords.latitude, lng: loc.coords.longitude });
    })();
    loadAsignaciones();
    loadGeocercas();
  }, []);

  const loadAsignaciones = async () => {
    try {
      const { data } = await api.get('/calificaciones');
      if (data && data.asignaciones) {
        setAsignaciones(data.asignaciones);
      } else {
        const arr = data || [];
        const m = new Map();
        arr.forEach(c => { if (c.asignacion) m.set(c.asignacion.id_asignacion, c.asignacion); });
        setAsignaciones(Array.from(m.values()));
      }
    } catch (_) {}
  };

  const loadGeocercas = async () => {
    try {
      const { data } = await api.get('/geocercas');
      setGeocercas(data);
    } catch (_) {}
  };

  const refreshLocation = async () => {
    const loc = await Location.getCurrentPositionAsync({ accuracy: Location.Accuracy.High });
    setLocation({ lat: loc.coords.latitude, lng: loc.coords.longitude });
    Alert.alert('Ubicación actualizada', `${loc.coords.latitude.toFixed(6)}, ${loc.coords.longitude.toFixed(6)}`);
  };

  const getGeocercaInfo = () => {
    if (!selectedAsig) return null;
    return geocercas.filter(g => g.id_asignacion === selectedAsig && g.activo);
  };

  const checkin = async () => {
    if (!selectedAsig || !location) { Alert.alert('Error', 'Seleccione asignación y active ubicación.'); return; }
    setLoading(true);
    setResult(null);
    try {
      const today = new Date().toISOString().split('T')[0];
      const res = await api.post('/asistencia/register', {
        id_asignacion: selectedAsig,
        latitud: location.lat,
        longitud: location.lng,
        fecha: today,
      });
      setResult(res.data);
    } catch (err) {
      const msg = err.response?.data?.message || 'Error al registrar.';
      const hora = err.response?.data?.hora_actual;
      Alert.alert('Error', hora ? `${msg} (hora actual: ${hora})` : msg);
    } finally {
      setLoading(false);
    }
  };

  const info = getGeocercaInfo();

  return (
    <ScrollView style={s.container} contentContainerStyle={s.content}>
      <Text style={s.title}>📍 Registrar Asistencia</Text>

      <View style={s.card}>
        <Text style={s.label}>Asignación</Text>
        <View style={s.pickerWrap}>
          <Picker
            selectedValue={selectedAsig}
            onValueChange={v => { setSelectedAsig(v); setResult(null); }}
            style={s.picker}
          >
            <Picker.Item label="-- Seleccione materia --" value={null} />
            {asignaciones.map(a => (
              <Picker.Item key={a.id_asignacion} label={`${a.materia?.nombre} - ${a.curso?.nombre}`} value={a.id_asignacion} />
            ))}
          </Picker>
        </View>
      </View>

      {info && info.length > 0 && (
        <View style={s.card}>
          <Text style={s.label}>Geocercas disponibles</Text>
          {info.map((g, i) => (
            <View key={i} style={s.geoInfo}>
              <Text style={s.geoName}>{g.nombre}</Text>
              <Text style={s.geoDetail}>
                Horario: {g.horario_inicio?.substring(0, 5) || '00:00'} - {g.horario_fin?.substring(0, 5) || '23:59'}
              </Text>
              {g.dias_semana && (
                <Text style={s.geoDetail}>
                  Días: {g.dias_semana.split(',').map(d => DIAS_LABEL[d] || d).join(', ')}
                </Text>
              )}
              <Text style={s.geoDetail}>Radio: {g.radio_metros}m</Text>
            </View>
          ))}
        </View>
      )}

      <View style={s.card}>
        <Text style={s.label}>Ubicación</Text>
        {location ? (
          <Text style={s.coords}>
            {location.lat.toFixed(6)}, {location.lng.toFixed(6)}
          </Text>
        ) : (
          <Text style={s.coords}>Obteniendo ubicación...</Text>
        )}
        <TouchableOpacity style={s.locBtn} onPress={refreshLocation}>
          <Text style={s.locBtnText}>🔄 Actualizar Ubicación</Text>
        </TouchableOpacity>
      </View>

      <TouchableOpacity style={[s.checkinBtn, (!selectedAsig || !location) && s.disabled]} onPress={checkin} disabled={loading || !selectedAsig || !location}>
        {loading ? <ActivityIndicator color="#fff" /> : <Text style={s.checkinText}>✅ Registrar Asistencia</Text>}
      </TouchableOpacity>

      {result && (
        <View style={[s.resultCard, result.asistencia?.estado === 'presente' ? s.success : s.fail]}>
          <Text style={s.resultTitle}>{result.mensaje}</Text>
          {result.geocerca && <Text style={s.resultDetail}>Geocerca: {result.geocerca.nombre}</Text>}
          {result.dentro_horario && <Text style={s.resultDetail}>Horario: {result.horario_inicio?.substring(0,5)} - {result.horario_fin?.substring(0,5)}</Text>}
          <Text style={s.resultDetail}>Estado: {result.asistencia?.estado}</Text>
        </View>
      )}
    </ScrollView>
  );
}

const s = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f0f2f5' },
  content: { padding: 20, paddingBottom: 40 },
  title: { fontSize: 22, fontWeight: '700', color: '#1a73e8', marginBottom: 20 },
  card: { backgroundColor: '#fff', borderRadius: 12, padding: 16, marginBottom: 16, shadowColor: '#000', shadowOpacity: 0.05, shadowRadius: 8, elevation: 2 },
  label: { fontSize: 13, fontWeight: '600', color: '#888', textTransform: 'uppercase', letterSpacing: 0.5, marginBottom: 10 },
  pickerWrap: { borderWidth: 1, borderColor: '#ddd', borderRadius: 8, overflow: 'hidden' },
  picker: { height: 50, color: '#333' },
  coords: { fontSize: 16, fontWeight: '600', color: '#333', marginBottom: 10, fontFamily: Platform.OS === 'ios' ? 'Menlo' : 'monospace' },
  locBtn: { backgroundColor: '#f0f2f5', borderRadius: 8, padding: 10, alignItems: 'center' },
  locBtnText: { fontSize: 13, fontWeight: '600', color: '#555' },
  checkinBtn: { backgroundColor: '#1a73e8', borderRadius: 12, padding: 18, alignItems: 'center', marginTop: 8 },
  disabled: { opacity: 0.5 },
  checkinText: { color: '#fff', fontSize: 17, fontWeight: '700' },
  resultCard: { borderRadius: 12, padding: 16, marginTop: 16 },
  success: { backgroundColor: '#e8f5e9' },
  fail: { backgroundColor: '#ffebee' },
  resultTitle: { fontSize: 16, fontWeight: '700', color: '#333', marginBottom: 6 },
  resultDetail: { fontSize: 13, color: '#555', marginTop: 2 },
  geoInfo: { backgroundColor: '#f8f9fa', borderRadius: 8, padding: 12, marginBottom: 8 },
  geoName: { fontSize: 14, fontWeight: '600', color: '#333', marginBottom: 4 },
  geoDetail: { fontSize: 12, color: '#666', marginTop: 1 },
});
