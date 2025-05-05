import { defineStore } from 'pinia';
import { ref } from 'vue';

type NotificationType = 'success' | 'error' | 'info';

interface Notification {
  id: number;
  title: string;
  message?: string;
  type: NotificationType;
  duration?: number;
}

export const useNotificationStore = defineStore('notification', () => {
  const notifications = ref<Notification[]>([]);
  let nextId = 0;

  function add(notification: Omit<Notification, 'id'>) {
    const id = nextId++;
    notifications.value.push({
      id,
      ...notification
    });
    
    return id;
  }

  function remove(id: number) {
    const index = notifications.value.findIndex(n => n.id === id);
    if (index !== -1) {
      notifications.value.splice(index, 1);
    }
  }

  function success(title: string, message?: string, duration = 3000) {
    return add({ title, message, type: 'success', duration });
  }

  function error(title: string, message?: string, duration = 5000) {
    return add({ title, message, type: 'error', duration });
  }

  function info(title: string, message?: string, duration = 3000) {
    return add({ title, message, type: 'info', duration });
  }

  return {
    notifications,
    add,
    remove,
    success,
    error,
    info
  };
});