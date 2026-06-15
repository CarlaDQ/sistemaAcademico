import React, { useEffect, useState } from 'react';
import { View, Text, StyleSheet, ScrollView, TouchableOpacity } from 'react-native';
import { useAuth } from '../auth/AuthContext';
import api from '../api/client';

export default function DashboardScreen({ navigation }) {
  const { user, logout } = useAuth();
  const [stats, setStats] = useState({ notificaciones: 0 });

  useEffect(() => {
    api.get('/notificaciones/unread-count')
      .then(r => setStats({ notificaciones: r.data.unread }))
      .catch(() => {});
  }, []);

  const roleMenu = {
    estudiante: [
      { icon: '📍', label: 'Registrar Asistencia', screen: 'AttendanceCheckin' },
      { icon: '📊', label: 'Ver Calificaciones', screen: 'Grades' },
      { icon: '📋', label: 'Mi Asistencia', screen: 'AttendanceHistory' },
    ],
    docente: [
      { icon: '📍', label: 'Registrar Asistencia', screen: 'AttendanceCheckin' },
      { icon: '📊', label: 'Calificaciones', screen: 'Grades' },
      { icon: '📋', label: 'Asistencia', screen: 'AttendanceHistory' },
      { icon: '🌐', label: 'Configurar Geocercas', screen: 'GeocercaManagement' },
    ],
    padre_familia: [
      { icon: '📊', label: 'Calificaciones', screen: 'Grades' },
      { icon: '📋', label: 'Asistencia', screen: 'AttendanceHistory' },
    ],
  };

  const menu = roleMenu[user?.rol] || [];

  return (
    <ScrollView style={s.container} contentContainerStyle={{ padding: 20 }}>
      <View style={s.profileCard}>
        <View style={s.avatar}>
          <Text style={s.avatarText}>{user?.nombre?.[0]}{user?.apellido?.[0]}</Text>
        </View>
        <Text style={s.userName}>{user?.nombre} {user?.apellido}</Text>
        <Text style={s.userRole}>{user?.rol?.replace('_', ' ')}</Text>
      </View>

      {stats.notificaciones > 0 && (
        <TouchableOpacity style={s.notifBar} onPress={() => navigation.navigate('Notifications')}>
          <Text style={s.notifText}>🔔 {stats.notificaciones} notificación(es) sin leer</Text>
        </TouchableOpacity>
      )}

      <Text style={s.sectionTitle}>Menú Principal</Text>
      <View style={s.menuGrid}>
        {menu.map((item, i) => (
          <TouchableOpacity key={i} style={s.menuCard} onPress={() => navigation.navigate(item.screen)}>
            <Text style={s.menuIcon}>{item.icon}</Text>
            <Text style={s.menuLabel}>{item.label}</Text>
          </TouchableOpacity>
        ))}
      </View>

      <TouchableOpacity style={s.logoutBtn} onPress={logout}>
        <Text style={s.logoutText}>Cerrar Sesión</Text>
      </TouchableOpacity>
    </ScrollView>
  );
}

const s = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f0f2f5' },
  profileCard: { backgroundColor: '#1a73e8', borderRadius: 16, padding: 24, alignItems: 'center', marginBottom: 20 },
  avatar: { width: 64, height: 64, borderRadius: 32, backgroundColor: 'rgba(255,255,255,0.25)', justifyContent: 'center', alignItems: 'center', marginBottom: 12 },
  avatarText: { fontSize: 24, fontWeight: '700', color: '#fff' },
  userName: { fontSize: 20, fontWeight: '700', color: '#fff' },
  userRole: { fontSize: 13, color: 'rgba(255,255,255,0.8)', marginTop: 4, textTransform: 'capitalize' },
  notifBar: { backgroundColor: '#fff3e0', borderRadius: 10, padding: 14, marginBottom: 20, borderLeftWidth: 4, borderLeftColor: '#e65100' },
  notifText: { fontSize: 14, fontWeight: '600', color: '#e65100' },
  sectionTitle: { fontSize: 16, fontWeight: '700', color: '#333', marginBottom: 12 },
  menuGrid: { flexDirection: 'row', flexWrap: 'wrap', gap: 12 },
  menuCard: { width: '47%', backgroundColor: '#fff', borderRadius: 14, padding: 20, alignItems: 'center', shadowColor: '#000', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.06, shadowRadius: 8, elevation: 3 },
  menuIcon: { fontSize: 32, marginBottom: 8 },
  menuLabel: { fontSize: 13, fontWeight: '600', color: '#333', textAlign: 'center' },
  logoutBtn: { marginTop: 24, padding: 14, borderRadius: 10, borderWidth: 1, borderColor: '#dc3545', alignItems: 'center' },
  logoutText: { color: '#dc3545', fontWeight: '600', fontSize: 15 },
});