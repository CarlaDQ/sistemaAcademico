import React, { useEffect, useState } from 'react';
import { View, Text, FlatList, StyleSheet } from 'react-native';
import api from '../api/client';

export default function GradesScreen() {
  const [data, setData] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    api.get('/calificaciones')
      .then(r => setData(r.data))
      .catch(() => {})
      .finally(() => setLoading(false));
  }, []);

  const gradeColor = (n) => {
    if (n >= 70) return '#2e7d32';
    if (n >= 40) return '#e65100';
    return '#c62828';
  };

  const groupByMateria = () => {
    const groups = {};
    data.forEach(c => {
      const key = c.asignacion?.materia?.nombre || 'General';
      if (!groups[key]) groups[key] = [];
      groups[key].push(c);
    });
    return groups;
  };

  const renderGroup = ({ item: [materia, califs] }) => {
    const prom = califs.reduce((s, c) => s + c.nota, 0) / califs.length;
    return (
      <View style={s.group}>
        <View style={s.groupHeader}>
          <Text style={s.materia}>{materia}</Text>
          <View style={[s.promBadge, { backgroundColor: gradeColor(prom) + '20' }]}>
            <Text style={[s.promText, { color: gradeColor(prom) }]}>{prom.toFixed(1)}</Text>
          </View>
        </View>
        {califs.map((c, i) => (
          <View key={i} style={s.row}>
            <Text style={s.actividad}>{c.tipo_evaluacion?.nombre || 'N/A'}</Text>
            <Text style={[s.nota, { color: gradeColor(c.nota) }]}>{c.nota}</Text>
          </View>
        ))}
      </View>
    );
  };

  const groups = Object.entries(groupByMateria());

  return (
    <View style={s.container}>
      <Text style={s.title}>📊 Calificaciones</Text>
      {loading ? (
        <Text style={s.empty}>Cargando...</Text>
      ) : groups.length === 0 ? (
        <Text style={s.empty}>No hay calificaciones registradas.</Text>
      ) : (
        <FlatList data={groups} keyExtractor={([m]) => m} renderItem={renderGroup} contentContainerStyle={{ paddingBottom: 20 }} />
      )}
    </View>
  );
}

const s = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f0f2f5', padding: 20 },
  title: { fontSize: 22, fontWeight: '700', color: '#1a73e8', marginBottom: 20 },
  group: { backgroundColor: '#fff', borderRadius: 12, padding: 16, marginBottom: 12 },
  groupHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 12, paddingBottom: 8, borderBottomWidth: 1, borderBottomColor: '#eee' },
  materia: { fontSize: 16, fontWeight: '700', color: '#333' },
  promBadge: { paddingHorizontal: 12, paddingVertical: 4, borderRadius: 12 },
  promText: { fontSize: 14, fontWeight: '700' },
  row: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', paddingVertical: 6 },
  actividad: { fontSize: 14, color: '#555' },
  nota: { fontSize: 16, fontWeight: '700' },
  empty: { textAlign: 'center', color: '#888', marginTop: 40, fontSize: 15 },
});