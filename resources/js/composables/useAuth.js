import { ref } from 'vue';
import axios from 'axios';
import router from '../router';

const isClient = typeof window !== 'undefined' && window.localStorage;

const storedUserRaw = isClient ? localStorage.getItem('user') : null;

function safeParse(json) {
  if (typeof json !== 'string') return null;
  try {
    return JSON.parse(json);
  } catch {
    return null;
  }
}

const user = ref(safeParse(storedUserRaw));

const token = isClient ? localStorage.getItem('token') : null;

if (token) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

export function useAuth() {
  const setUser = (userData, token) => {
    user.value = userData;
    localStorage.setItem('user', JSON.stringify(userData));
    localStorage.setItem('token', token);
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
  };

  async function fetchUser() {
    try {
      const response = await axios.get('/api/auth/profile');
      console.log('fetchUser response:', response.data);
      user.value = response.data;
      localStorage.setItem('user', JSON.stringify(response.data));
    } catch (error) {
      console.error('Failed to fetch user:', error);
      if (error.response && error.response.status === 401) {
        logout();
      }
    }
  }
  

  const logout = async () => {
    try {
      await axios.post('/api/auth/logout');
    } catch (err) {
      console.error('Error logging out from server:', err);
    }

    // مسح البيانات المحلية
    user.value = null;
    localStorage.removeItem('user');
    localStorage.removeItem('token');
    delete axios.defaults.headers.common['Authorization'];

    // توجيه المستخدم إلى صفحة تسجيل الدخول
    router.push('/login');
  };

  return {
    user,
    setUser,
    logout,
    fetchUser,

  };
}
