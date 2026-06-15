import React from 'react';
import { ActivityIndicator, View, StyleSheet } from 'react-native';
import { NavigationContainer } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { useAuth } from '../auth/AuthContext';
import LoginScreen from '../screens/LoginScreen';
import DashboardScreen from '../screens/DashboardScreen';
import AttendanceCheckinScreen from '../screens/AttendanceCheckinScreen';
import AttendanceHistoryScreen from '../screens/AttendanceHistoryScreen';
import GradesScreen from '../screens/GradesScreen';
import NotificationsScreen from '../screens/NotificationsScreen';
import GeocercaManagementScreen from '../screens/GeocercaManagementScreen';

const Stack = createNativeStackNavigator();

export default function AppNavigator() {
  const { user, loading } = useAuth();

  if (loading) {
    return (
      <View style={s.loading}>
        <ActivityIndicator size="large" color="#1a73e8" />
      </View>
    );
  }

  return (
    <NavigationContainer>
      <Stack.Navigator
        screenOptions={{
          headerStyle: { backgroundColor: '#1a73e8' },
          headerTintColor: '#fff',
          headerTitleStyle: { fontWeight: '700' },
        }}
      >
        {!user ? (
          <Stack.Screen name="Login" component={LoginScreen} options={{ headerShown: false }} />
        ) : (
          <>
            <Stack.Screen name="Dashboard" component={DashboardScreen} options={{ title: 'SGA Móvil' }} />
            <Stack.Screen name="AttendanceCheckin" component={AttendanceCheckinScreen} options={{ title: 'Registrar Asistencia' }} />
            <Stack.Screen name="AttendanceHistory" component={AttendanceHistoryScreen} options={{ title: 'Historial de Asistencia' }} />
            <Stack.Screen name="Grades" component={GradesScreen} options={{ title: 'Calificaciones' }} />
            <Stack.Screen name="Notifications" component={NotificationsScreen} options={{ title: 'Notificaciones' }} />
            <Stack.Screen name="GeocercaManagement" component={GeocercaManagementScreen} options={{ title: 'Geocercas' }} />
          </>
        )}
      </Stack.Navigator>
    </NavigationContainer>
  );
}

const s = StyleSheet.create({
  loading: { flex: 1, justifyContent: 'center', alignItems: 'center', backgroundColor: '#f0f2f5' },
});