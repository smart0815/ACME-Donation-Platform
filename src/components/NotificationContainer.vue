<template>
  <div class="fixed inset-0 flex flex-col items-end justify-start pt-4 px-4 pointer-events-none space-y-4 z-50">
    <Notification
      v-for="notification in notificationStore.notifications"
      :key="notification.id"
      :title="notification.title"
      :message="notification.message"
      :type="notification.type"
      :duration="notification.duration"
      @close="notificationStore.remove(notification.id)"
    />
  </div>
</template>

<script setup lang="ts">
import { useNotificationStore } from '@/stores/notification';
import Notification from './Notification.vue';

const notificationStore = useNotificationStore();
</script>