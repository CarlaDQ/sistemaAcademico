import React, { useEffect, useState } from 'react';
import { View, Text, FlatList, StyleSheet, TouchableOpacity, Alert } from 'react-native';
import api from '../api/client';

export default function NotificationsScreen() {
  const [data, setData] = useState([]);

  useEffect(() => { load(); }, []);

  const load = async () => {
    try {
      const res = await api.get('/notificaciones');
      setData(res.data);
    } catch (_) {}
  };

  const markRead = async (id) => {
    try {
      await api.put(`/notificaciones/${id}/read`);
      setData(prev => prev.map(n => n.id_notificacion === id ? { ...n, leido: true } : n));
    } catch (_) {}
  };

  const renderItem = ({ item }) => (
    <TouchableOpacity
      style={[s.card, !item.leido && s.unread]}
      onPress={() => !item.leido && markRead(item.id_notificacion)}
    >
      <View style={s.row}>
        <Text style={[s.title, !item.leido && s.bold]}>{item.titulo}</Text>
        {!item.leido && <View style={s.dot} />}
      </View>
      <Text style={s.body}>{item.mensaje}</Text>
      <Text style={s.date}>{item.created_at}</Text>
    </TouchableOpacity>
  );

  return (
    <View style={s.container}>
      <Text style={s.header}>🔔 Notificaciones</Text>
      {data.length === 0 ? (
        <Text style={s.empty}>No hay notificaciones.</Text>
      ) : (
        <FlatList data={data} keyExtractor={(_, i) => String(i)} renderItem={renderItem} />
      )}
    </View>
  );
}

const s = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f0f2f5', padding: 20 },
  header: { fontSize: 22, fontWeight: '700', color: '#1a73e8', marginBottom: 20 },
  card: { backgroundColor: '#fff', borderRadius: 12, padding: 16, marginBottom: 10 },
  unread: { borderLeftWidth: 4, borderLeftColor: '#1a73e8' },
  row: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' },
  title: { fontSize: 15, color: '#333', flex: 1 },
  bold: { fontWeight: '700' },
  dot: { width: 8, height: 8, borderRadius: 4, backgroundColor: '#1a73e8', marginLeft: 8 },
  body: { fontSize: 13, color: '#666', marginTop: 6 },
  date: { fontSize: 11, color: '#aaa', marginTop: 8 },
  empty: { textAlign: 'center', color: '#888', marginTop: 40 },
});