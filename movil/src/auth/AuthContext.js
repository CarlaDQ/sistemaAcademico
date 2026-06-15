import React, { createContext, useState, useEffect, useContext } from 'react';
import AsyncStorage from '@react-native-async-storage/async-storage';
import api from '../api/client';

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => { restoreSession(); }, []);

  const restoreSession = async () => {
    try {
      const stored = await AsyncStorage.getItem('user_data');
      const token = await AsyncStorage.getItem('auth_token');
      if (stored && token) {
        setUser(JSON.parse(stored));
        const res = await api.get('/auth/profile');
        setUser(res.data);
        await AsyncStorage.setItem('user_data', JSON.stringify(res.data));
      }
    } catch (_) { await logout(); }
    finally { setLoading(false); }
  };

  const login = async (email, password) => {
    const res = await api.post('/auth/login', { email, password });
    await AsyncStorage.setItem('auth_token', res.data.token);
    await AsyncStorage.setItem('user_data', JSON.stringify(res.data.user));
    setUser(res.data.user);
    return res.data;
  };

  const logout = async () => {
    try { await api.post('/auth/logout'); } catch (_) {}
    await AsyncStorage.multiRemove(['auth_token', 'user_data']);
    setUser(null);
  };

  return (
    <AuthContext.Provider value={{ user, loading, login, logout }}>
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() { return useContext(AuthContext); }