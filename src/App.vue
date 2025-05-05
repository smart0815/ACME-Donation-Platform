<template>
  <div class="min-h-screen bg-gray-50">
    <nav v-if="authStore.isAuthenticated" class="bg-white shadow">
      <!-- Navigation bar with links to Campaigns and Admin (for admins) -->
    </nav>

    <main>
      <router-view v-slot="{ Component }">
        <component :is="Component" />
      </router-view>
    </main>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';

const router = useRouter();
const authStore = useAuthStore();

onMounted(() => {
  if (!authStore.isAuthenticated) {
    router.push('/login');
  }
});
</script>