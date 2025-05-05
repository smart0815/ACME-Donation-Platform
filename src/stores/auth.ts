import { defineStore } from 'pinia';
import { ref, computed, onMounted } from 'vue';
import { auth } from '@/lib/api';
import type { User } from '@/types';

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null);
  const loading = ref(false);
  const initialized = ref(false);

  const isAuthenticated = computed(() => !!user.value);
  const isAdmin = computed(() => user.value?.role === 'admin');

  // Initialize auth state from token
  async function init() {
    const token = localStorage.getItem('token');
    if (token) {
      try {
        loading.value = true;
        const { data } = await auth.getUser();
        user.value = data;
      } catch (error) {
        console.error('Error initializing auth:', error);
        localStorage.removeItem('token');
      } finally {
        loading.value = false;
        initialized.value = true;
      }
    } else {
      initialized.value = true;
    }
  }

  async function register(email: string, password: string, fullName: string) {
    try {
      loading.value = true;
      const { data } = await auth.register({ email, password, full_name: fullName });
      user.value = data.user;
      localStorage.setItem('token', data.token);
      return data;
    } catch (error) {
      console.error('Error registering:', error);
      throw error;
    } finally {
      loading.value = false;
    }
  }

  async function login(email: string, password: string) {
    try {
      loading.value = true;
      const { data } = await auth.login({ email, password });
      user.value = data.user;
      localStorage.setItem('token', data.token);
      return data;
    } catch (error) {
      console.error('Error logging in:', error);
      throw error;
    } finally {
      loading.value = false;
    }
  }

  async function logout() {
    try {
      loading.value = true;
      await auth.logout();
      user.value = null;
      localStorage.removeItem('token');
    } catch (error) {
      console.error('Error logging out:', error);
      // Still clear local state even if API call fails
      user.value = null;
      localStorage.removeItem('token');
      throw error;
    } finally {
      loading.value = false;
    }
  }

  // Update API client to use the new token
  function updateApiToken(token: string) {
    localStorage.setItem('token', token);
  }

  // Initialize on store creation
  onMounted(() => {
    init();
  });

  return {
    user,
    loading,
    initialized,
    isAuthenticated,
    isAdmin,
    register,
    login,
    logout,
    updateApiToken,
    init
  };
});