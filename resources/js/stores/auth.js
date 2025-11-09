import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/api/client';

export const useAuthStore = defineStore('auth', () => {
  const router = useRouter();
  const student = ref(null);
  const token = ref(null);
  const isAuthenticated = computed(() => !!token.value);

  // Initialize from localStorage
  const initAuth = () => {
    const savedToken = localStorage.getItem('token');
    const savedStudent = localStorage.getItem('student');
    
    if (savedToken && savedStudent) {
      token.value = savedToken;
      student.value = JSON.parse(savedStudent);
      api.defaults.headers.common['Authorization'] = `Bearer ${savedToken}`;
    }
  };

  // Login
  const login = async (email, password) => {
    try {
      const response = await api.post('/students/login', { email, password });
      
      if (response.data.student) {
        student.value = response.data.student;
        token.value = response.data.token;
        
        localStorage.setItem('token', response.data.token);
        localStorage.setItem('student', JSON.stringify(response.data.student));
        
        api.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
        
        return { success: true };
      }
      
      return { success: false, message: 'Invalid credentials' };
    } catch (error) {
      return { 
        success: false, 
        message: error.response?.data?.message || 'Login failed. Please try again.' 
      };
    }
  };

  // Register
  const register = async (name, email, password, passwordConfirmation) => {
    try {
      const response = await api.post('/students/register', {
        name,
        email,
        password,
        password_confirmation: passwordConfirmation
      });
      
      if (response.data.student) {
        student.value = response.data.student;
        token.value = response.data.token;
        
        localStorage.setItem('token', response.data.token);
        localStorage.setItem('student', JSON.stringify(response.data.student));
        
        api.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
        
        return { success: true };
      }
      
      return { success: false, message: 'Registration failed' };
    } catch (error) {
      return { 
        success: false, 
        message: error.response?.data?.message || 'Registration failed. Please try again.' 
      };
    }
  };

  // Logout
  const logout = () => {
    student.value = null;
    token.value = null;
    
    localStorage.removeItem('token');
    localStorage.removeItem('student');
    
    delete api.defaults.headers.common['Authorization'];
    
    router.push('/login');
  };

  // Initialize on store creation
  initAuth();

  return {
    student,
    token,
    isAuthenticated,
    login,
    register,
    logout,
  };
});
