import axios from 'axios';

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

// Add request interceptor for authentication
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Add response interceptor for error handling
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Handle unauthorized access
      localStorage.removeItem('token');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

export const campaigns = {
  list: () => api.get('/api/campaigns'),
  create: (data: any) => api.post('/api/campaigns', data),
  get: (id: string) => api.get(`/api/campaigns/${id}`),
  donate: (id: string, data: any) => api.post(`/api/campaigns/${id}/donate`, data)
};

export const auth = {
  login: (credentials: any) => api.post('/api/auth/login', credentials),
  register: (data: any) => api.post('/api/auth/register', data),
  logout: () => api.post('/api/auth/logout')
};

export const stats = {
  dashboard: () => api.get('/api/stats/dashboard'),
  userContributions: () => api.get('/api/stats/user-contributions')
};

export default api;