import axios from 'axios';

// إعداد أساسيات Axios
const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api',
  headers: {
    'Accept': 'application/json',
  },
});

api.interceptors.request.use(config => {
  const token = localStorage.getItem('token'); // توكن المخزن في localStorage
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
}, error => {
  return Promise.reject(error);
});

export default api;
