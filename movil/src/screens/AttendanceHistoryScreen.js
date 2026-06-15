import React, { useEffect, useState } from 'react';
import { View, Text, FlatList, StyleSheet } from 'react-native';
import api from '../api/client';

export default function AttendanceHistoryScreen() {
  const [data, setData] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    api.get('/asistencia/history')
      .then(r => setData(r.data))
      .catch(() => {})
      .finally(() => setLoading(false));
  }, []);

  const statusColor = (s) => {
    switch (s) {
      case 'presente': return '#2e7d32';
      case 'ausente': return '#c62828';
      case 'tardanza': return '#e65100';
      case 'justificado': return '#1565c0';
      default: return '#888';
    }
  };

  const renderItem = ({ item }) => (
    <View style={s.card}>
      <View style={s.row}>
        <Text style={s.materia}>{item.asignacion?.materia?.nombre || 'N/A'}</Text>
        <View style={[s.badge, { backgroundColor: statusColor(item.estado) + '20' }]}>
          <Text style={[s.badgeText, { color: statusColor(item.estado) }]}>{item.estado}</Text>
        </View>
      </View>
      <Text style={s.detail}>Fecha: {item.fecha}</Text>
      {item.estudiante?.usuario && (
        <Text style={s.detail}>Estudiante: {item.estudiante.usuario.nombre} {item.estudiante.usuario.apellido}</Text>
      )}
    </View>
  );

  return (
    <View style={s.container}>
      <Text style={s.title}>📋 Historial de Asistencia</Text>
      {loading ? (
        <Text style={s.empty}>Cargando...</Text>
      ) : data.length === 0 ? (
        <Text style={s.empty}>No hay registros de asistencia.</Text>
      ) : (
        <FlatList data={data} keyExtractor={(_, i) => String(i)} renderItem={renderItem} contentContainerStyle={{ paddingBottom: 20 }} />
      )}
    </View>
  );
}

const s = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f0f2f5', padding: 20 },
  title: { fontSize: 22, fontWeight: '700', color: '#1a73e8', marginBottom: 20 },
  card: { backgroundColor: '#fff', borderRadius: 12, padding: 16, marginBottom: 10, shadowColor: '#000', shadowOpacity: 0.04, shadowRadius: 6, elevation: 2 },
  row: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 8 },
  materia: { fontSize: 16, fontWeight: '700', color: '#333', flex: 1 },
  badge: { paddingHorizontal: 10, paddingVertical: 4, borderRadius: 12 },
  badgeText: { fontSize: 12, fontWeight: '700', textTransform: 'capitalize' },
  detail: { fontSize: 13, color: '#666', marginTop: 2 },
  empty: { textAlign: 'center', color: '#888', marginTop: 40, fontSize: 15 },
});