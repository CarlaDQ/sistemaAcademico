import React, { useEffect, useState } from 'react';
import {
  View, Text, FlatList, StyleSheet, TouchableOpacity, TextInput,
  Alert, Modal, ActivityIndicator, ScrollView, Platform,
} from 'react-native';
import MapView, { Marker } from 'react-native-maps';
import * as Location from 'expo-location';
import api from '../api/client';

const DIAS = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
const DIAS_LABEL = { lunes: 'Lun', martes: 'Mar', miercoles: 'Mié', jueves: 'Jue', viernes: 'Vie', sabado: 'Sáb' };

export default function GeocercaManagementScreen() {
  const [geocercas, setGeocercas] = useState([]);
  const [loading, setLoading] = useState(true);
  const [modal, setModal] = useState(false);
  const [form, setForm] = useState({
    id_asignacion: '', nombre: '', descripcion: '',
    latitud_centro: '', longitud_centro: '', radio_metros: '100',
    horario_inicio: '14:00', horario_fin: '18:00',
    dias_semana: [],
  });
  const [asignaciones, setAsignaciones] = useState([]);
  const [mapRegion, setMapRegion] = useState(null);
  const [showMap, setShowMap] = useState(false);

  useEffect(() => { load(); loadAsignaciones(); }, []);

  const load = async () => {
    try { const r = await api.get('/geocercas'); setGeocercas(r.data); } catch (_) {}
    finally { setLoading(false); }
  };

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

  const useCurrentLocation = async () => {
    try {
      const { status } = await Location.requestForegroundPermissionsAsync();
      if (status !== 'granted') { Alert.alert('Permiso denegado'); return; }
      const loc = await Location.getCurrentPositionAsync({ accuracy: Location.Accuracy.High });
      const lat = loc.coords.latitude.toFixed(6);
      const lng = loc.coords.longitude.toFixed(6);
      setForm({ ...form, latitud_centro: lat, longitud_centro: lng });
      setMapRegion({
        latitude: parseFloat(lat), longitude: parseFloat(lng),
        latitudeDelta: 0.01, longitudeDelta: 0.01,
      });
    } catch (e) { Alert.alert('Error', 'No se pudo obtener la ubicación.'); }
  };

  const create = async () => {
    if (!form.id_asignacion || !form.nombre || !form.latitud_centro || !form.longitud_centro) {
      Alert.alert('Error', 'Complete todos los campos obligatorios.');
      return;
    }
    try {
      await api.post('/geocercas', {
        ...form,
        latitud_centro: parseFloat(form.latitud_centro),
        longitud_centro: parseFloat(form.longitud_centro),
        radio_metros: parseInt(form.radio_metros),
        dias_semana: form.dias_semana.length ? form.dias_semana.join(',') : 'lunes,martes,miercoles,jueves,viernes',
      });
      setModal(false);
      setForm({ id_asignacion: '', nombre: '', descripcion: '', latitud_centro: '', longitud_centro: '', radio_metros: '100', horario_inicio: '14:00', horario_fin: '18:00', dias_semana: [] });
      setShowMap(false);
      load();
    } catch (err) {
      Alert.alert('Error', err.response?.data?.message || 'Error al crear.');
    }
  };

  const remove = (id) => {
    Alert.alert('Eliminar', '¿Eliminar esta geocerca?', [
      { text: 'Cancelar', style: 'cancel' },
      { text: 'Eliminar', style: 'destructive', onPress: async () => {
        try { await api.delete(`/geocercas/${id}`); load(); } catch (_) {}
      }},
    ]);
  };

  const toggleDia = (d) => {
    setForm({
      ...form,
      dias_semana: form.dias_semana.includes(d)
        ? form.dias_semana.filter(x => x !== d)
        : [...form.dias_semana, d],
    });
  };

  const onMapPress = (e) => {
    const { latitude, longitude } = e.nativeEvent.coordinate;
    setForm({ ...form, latitud_centro: latitude.toFixed(6), longitud_centro: longitude.toFixed(6) });
  };

  const renderItem = ({ item }) => {
    const horario = item.horario_inicio
      ? `${item.horario_inicio?.substring(0, 5)} - ${item.horario_fin?.substring(0, 5)}`
      : 'Sin horario';
    const dias = item.dias_semana
      ? item.dias_semana.split(',').map(d => DIAS_LABEL[d] || d).join(', ')
      : 'Todos';

    return (
      <View style={s.card}>
        <View style={s.cardHeader}>
          <Text style={s.cardTitle}>{item.nombre}</Text>
          <TouchableOpacity onPress={() => remove(item.id_geocerca)}><Text style={s.del}>✕</Text></TouchableOpacity>
        </View>
        <Text style={s.detail}>Asignación: {item.asignacion?.materia?.nombre} - {item.asignacion?.curso?.nombre}</Text>
        <Text style={s.detail}>Centro: {item.latitud_centro}, {item.longitud_centro}</Text>
        <Text style={s.detail}>Radio: {item.radio_metros}m</Text>
        <Text style={s.detail}>Horario: {horario}</Text>
        <Text style={s.detail}>Días: {dias}</Text>
        {item.descripcion && <Text style={s.detail}>{item.descripcion}</Text>}
      </View>
    );
  };

  return (
    <View style={s.container}>
      <View style={s.headerRow}>
        <Text style={s.title}>🌐 Geocercas</Text>
        <TouchableOpacity style={s.addBtn} onPress={() => { setShowMap(false); setModal(true); }}>
          <Text style={s.addBtnText}>+ Nueva</Text>
        </TouchableOpacity>
      </View>

      {loading ? <ActivityIndicator style={{ marginTop: 40 }} /> : geocercas.length === 0 ? (
        <Text style={s.empty}>No hay geocercas configuradas.</Text>
      ) : (
        <FlatList data={geocercas} keyExtractor={(_, i) => String(i)} renderItem={renderItem} />
      )}

      <Modal visible={modal} animationType="slide" transparent>
        <ScrollView style={s.modal} keyboardShouldPersistTaps="handled">
          <Text style={s.modalTitle}>Nueva Geocerca</Text>

          {!showMap ? (
            <>
              <Text style={s.label}>Asignación *</Text>
              {asignaciones.map(a => (
                <TouchableOpacity key={a.id_asignacion} style={[s.opt, form.id_asignacion === a.id_asignacion && s.optSel]} onPress={() => setForm({ ...form, id_asignacion: a.id_asignacion })}>
                  <Text style={[s.optText, form.id_asignacion === a.id_asignacion && s.optTextSel]}>{a.materia?.nombre} - {a.curso?.nombre}</Text>
                </TouchableOpacity>
              ))}

              <Text style={s.label}>Nombre *</Text>
              <TextInput style={s.input} value={form.nombre} onChangeText={v => setForm({ ...form, nombre: v })} />

              <Text style={s.label}>Descripción</Text>
              <TextInput style={s.input} value={form.descripcion} onChangeText={v => setForm({ ...form, descripcion: v })} />

              <Text style={s.label}>Ubicación (Latitud / Longitud) *</Text>
              <View style={s.row}>
                <TextInput style={[s.input, s.flex1]} value={form.latitud_centro} onChangeText={v => setForm({ ...form, latitud_centro: v })} keyboardType="decimal-pad" placeholder="Latitud" />
                <TextInput style={[s.input, s.flex1, { marginLeft: 8 }]} value={form.longitud_centro} onChangeText={v => setForm({ ...form, longitud_centro: v })} keyboardType="decimal-pad" placeholder="Longitud" />
              </View>
              <TouchableOpacity style={s.mapBtn} onPress={useCurrentLocation}>
                <Text style={s.mapBtnText}>📍 Usar mi ubicación actual</Text>
              </TouchableOpacity>
              <TouchableOpacity style={s.mapBtn} onPress={() => {
                const lat = parseFloat(form.latitud_centro) || -17.7833;
                const lng = parseFloat(form.longitud_centro) || -63.1822;
                setMapRegion({ latitude: lat, longitude: lng, latitudeDelta: 0.02, longitudeDelta: 0.02 });
                setShowMap(true);
              }}>
                <Text style={s.mapBtnText}>🗺️ Seleccionar en el mapa</Text>
              </TouchableOpacity>

              <Text style={s.label}>Radio (metros) *</Text>
              <TextInput style={s.input} value={form.radio_metros} onChangeText={v => setForm({ ...form, radio_metros: v })} keyboardType="number-pad" />

              <Text style={s.label}>Horario para marcar asistencia</Text>
              <View style={s.row}>
                <View style={s.flex1}>
                  <Text style={s.subLabel}>Desde</Text>
                  <TextInput style={s.input} value={form.horario_inicio} onChangeText={v => setForm({ ...form, horario_inicio: v })} placeholder="14:00" />
                </View>
                <View style={[s.flex1, { marginLeft: 8 }]}>
                  <Text style={s.subLabel}>Hasta</Text>
                  <TextInput style={s.input} value={form.horario_fin} onChangeText={v => setForm({ ...form, horario_fin: v })} placeholder="18:00" />
                </View>
              </View>

              <Text style={s.label}>Días de la semana</Text>
              <View style={s.diasRow}>
                {DIAS.map(d => (
                  <TouchableOpacity key={d} style={[s.diaBtn, form.dias_semana.includes(d) && s.diaBtnSel]} onPress={() => toggleDia(d)}>
                    <Text style={[s.diaText, form.dias_semana.includes(d) && s.diaTextSel]}>{DIAS_LABEL[d]}</Text>
                  </TouchableOpacity>
                ))}
              </View>
            </>
          ) : (
            <>
              <Text style={s.modalTitle}>Seleccionar en el mapa</Text>
              <Text style={s.hint}>Toca en el mapa para colocar el marcador</Text>
              <View style={s.mapContainer}>
                {mapRegion && (
                  <MapView
                    style={s.map}
                    initialRegion={mapRegion}
                    onPress={onMapPress}
                  >
                    {form.latitud_centro && form.longitud_centro && (
                      <Marker
                        coordinate={{ latitude: parseFloat(form.latitud_centro), longitude: parseFloat(form.longitud_centro) }}
                        draggable
                        onDragEnd={(e) => {
                          const { latitude, longitude } = e.nativeEvent.coordinate;
                          setForm({ ...form, latitud_centro: latitude.toFixed(6), longitud_centro: longitude.toFixed(6) });
                        }}
                      />
                    )}
                  </MapView>
                )}
              </View>
              <View style={s.coordsBar}>
                <Text style={s.coordsText}>Lat: {form.latitud_centro || '---'}</Text>
                <Text style={s.coordsText}>Lng: {form.longitud_centro || '---'}</Text>
              </View>
              <TouchableOpacity style={s.mapBtn} onPress={useCurrentLocation}>
                <Text style={s.mapBtnText}>📍 Ir a mi ubicación</Text>
              </TouchableOpacity>
              <TouchableOpacity style={s.mapBtn} onPress={() => setShowMap(false)}>
                <Text style={s.mapBtnText}>✓ Confirmar ubicación</Text>
              </TouchableOpacity>
            </>
          )}

          <View style={s.modalBtns}>
            <TouchableOpacity style={s.cancelBtn} onPress={() => { setModal(false); setShowMap(false); }}><Text style={s.cancelText}>Cancelar</Text></TouchableOpacity>
            {!showMap && (
              <TouchableOpacity style={s.saveBtn} onPress={create}><Text style={s.saveText}>Guardar</Text></TouchableOpacity>
            )}
          </View>
        </ScrollView>
      </Modal>
    </View>
  );
}

const s = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f0f2f5', padding: 20 },
  headerRow: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 20 },
  title: { fontSize: 22, fontWeight: '700', color: '#1a73e8' },
  addBtn: { backgroundColor: '#1a73e8', paddingHorizontal: 16, paddingVertical: 8, borderRadius: 8 },
  addBtnText: { color: '#fff', fontWeight: '600', fontSize: 14 },
  card: { backgroundColor: '#fff', borderRadius: 12, padding: 16, marginBottom: 10 },
  cardHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 8 },
  cardTitle: { fontSize: 16, fontWeight: '700', color: '#333' },
  del: { color: '#dc3545', fontSize: 18, fontWeight: '700' },
  detail: { fontSize: 13, color: '#666', marginTop: 2 },
  empty: { textAlign: 'center', color: '#888', marginTop: 40, fontSize: 15 },
  modal: { flex: 1, backgroundColor: '#fff', padding: 20, marginTop: 60, borderTopLeftRadius: 20, borderTopRightRadius: 20 },
  modalTitle: { fontSize: 20, fontWeight: '700', color: '#333', marginBottom: 20 },
  hint: { fontSize: 13, color: '#888', marginBottom: 12 },
  label: { fontSize: 13, fontWeight: '600', color: '#555', marginTop: 14, marginBottom: 6 },
  subLabel: { fontSize: 11, color: '#888', marginBottom: 4 },
  input: { borderWidth: 1, borderColor: '#ddd', borderRadius: 8, padding: 12, fontSize: 15, backgroundColor: '#fafafa' },
  opt: { padding: 10, borderRadius: 8, borderWidth: 1, borderColor: '#eee', marginBottom: 4 },
  optSel: { borderColor: '#1a73e8', backgroundColor: '#e3f2fd' },
  optText: { fontSize: 13, color: '#333' },
  optTextSel: { fontWeight: '600', color: '#1a73e8' },
  row: { flexDirection: 'row', alignItems: 'center' },
  flex1: { flex: 1 },
  mapBtn: { backgroundColor: '#e3f2fd', borderRadius: 8, padding: 12, alignItems: 'center', marginTop: 8 },
  mapBtnText: { color: '#1a73e8', fontWeight: '600', fontSize: 13 },
  mapContainer: { height: 300, borderRadius: 12, overflow: 'hidden', marginBottom: 8 },
  map: { flex: 1 },
  coordsBar: { flexDirection: 'row', justifyContent: 'space-between', backgroundColor: '#f8f9fa', borderRadius: 8, padding: 10, marginBottom: 8 },
  coordsText: { fontSize: 12, fontFamily: Platform.OS === 'ios' ? 'Menlo' : 'monospace', color: '#555' },
  diasRow: { flexDirection: 'row', flexWrap: 'wrap', gap: 6, marginTop: 4 },
  diaBtn: { paddingHorizontal: 14, paddingVertical: 8, borderRadius: 20, borderWidth: 1, borderColor: '#ddd', backgroundColor: '#fafafa' },
  diaBtnSel: { borderColor: '#1a73e8', backgroundColor: '#1a73e8' },
  diaText: { fontSize: 13, color: '#666' },
  diaTextSel: { color: '#fff', fontWeight: '600' },
  modalBtns: { flexDirection: 'row', gap: 12, marginTop: 24 },
  cancelBtn: { flex: 1, padding: 14, borderRadius: 10, borderWidth: 1, borderColor: '#ddd', alignItems: 'center' },
  cancelText: { color: '#666', fontWeight: '600' },
  saveBtn: { flex: 1, padding: 14, borderRadius: 10, backgroundColor: '#1a73e8', alignItems: 'center' },
  saveText: { color: '#fff', fontWeight: '600' },
});
