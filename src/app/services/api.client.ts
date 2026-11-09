import axios from 'axios';
import { environment } from '../../environments/environment';

export const TOKEN_KEY = 'eduportal_token';
export const USER_KEY = 'eduportal_user';

export const api = axios.create({
  baseURL: environment.apiUrl,
  headers: { Accept: 'application/json' },
});

// Adjunta el token Bearer a cada peticion autenticada
api.interceptors.request.use((config) => {
  const token = sessionStorage.getItem(TOKEN_KEY);
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});
