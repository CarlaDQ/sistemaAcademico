import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

// CAMBIAR por la IP local de tu PC (ej: 192.168.1.X)
// Para Android emulator: http://10.0.2.2/...
// Para iPhone/iPad en misma WiFi: http://192.168.100.64/...
const API_URL = 'http://192.168.100.64/sistemaacademico-laravel/public/api';

const client = axios.create({ baseURL: API_URL });

client.interceptors.request.use(async (config) => {
  const token = await AsyncStorage.getItem('auth_token');
  if (token) config.headers.Authorization = `Bearer ${token}`;
  return config;
});

client.interceptors.response.use(
  (res) => res,
  (err) => {
    if (err.response?.status === 401) AsyncStorage.removeItem('auth_token');
    return Promise.reject(err);
  }
);

export default client;