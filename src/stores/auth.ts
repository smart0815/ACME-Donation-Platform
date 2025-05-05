import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { auth } from '@/lib/api';
import type { User } from '@/types';

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null);
  const loading = ref(false);

  const isAuthenticated = computed(() => !!user.value);
  const isAdmin = computed(() => user.value?.role === 'admin');

  async function register(email: string, password: string, fullName: string) {
    try {
      loading.value = true;
      const { data } = await auth.register({ email, password, full_name: fullName });
      user.value = data.user;
      localStorage.setItem('token', data.token);
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
      throw error;
    } finally {
      loading.value = false;
    }
  }

  return {
    user,
    loading,
    isAuthenticated,
    isAdmin,
    register,
    login,
    logout
  };
});