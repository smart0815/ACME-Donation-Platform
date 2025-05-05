<template>
  <div v-if="isOpen" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg overflow-hidden shadow-xl max-w-md w-full">
      <div class="px-6 py-4">
        <div class="flex justify-between items-center">
          <h3 class="text-lg font-medium text-gray-900">Donate to {{ campaign?.title }}</h3>
          <button @click="close" class="text-gray-400 hover:text-gray-500">
            <span class="sr-only">Close</span>
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        
        <form @submit.prevent="submitDonation" class="mt-4">
          <div class="mb-4">
            <label for="amount" class="block text-sm font-medium text-gray-700">Donation Amount ($)</label>
            <input
              type="number"
              id="amount"
              v-model="amount"
              min="1"
              step="1"
              required
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            />
          </div>
          
          <div class="mb-4">
            <label for="message" class="block text-sm font-medium text-gray-700">Message (Optional)</label>
            <textarea
              id="message"
              v-model="message"
              rows="3"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            ></textarea>
          </div>
          
          <div class="mb-4">
            <div class="flex items-center">
              <input
                id="anonymous"
                type="checkbox"
                v-model="anonymous"
                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
              />
              <label for="anonymous" class="ml-2 block text-sm text-gray-900">Make donation anonymous</label>
            </div>
          </div>
          
          <div class="mt-6 flex justify-end">
            <button
              type="button"
              @click="close"
              class="mr-3 inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="loading"
              class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
              {{ loading ? 'Processing...' : 'Donate' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, defineProps, defineEmits } from 'vue';
import { supabase } from '@/lib/supabaseClient';
import { useAuthStore } from '@/stores/auth';
import type { Campaign } from '@/types';

const props = defineProps<{
  isOpen: boolean;
  campaign: Campaign | null;
}>();

const emit = defineEmits(['close', 'donated']);

const authStore = useAuthStore();
const amount = ref(10);
const message = ref('');
const anonymous = ref(false);
const loading = ref(false);

function close() {
  emit('close');
}

async function submitDonation() {
  if (!props.campaign || !authStore.user) return;
  
  loading.value = true;
  
  try {
    const { data, error } = await supabase.from('donations').insert({
      amount: amount.value,
      message: message.value,
      campaign_id: props.campaign.id,
      donor_id: authStore.user.id,
      anonymous: anonymous.value,
      status: 'completed'
    });
    
    if (error) throw error;
    
    emit('donated', data);
    close();
  } catch (error) {
    console.error('Error submitting donation:', error);
    alert('Failed to process donation. Please try again.');
  } finally {
    loading.value = false;
  }
}
</script>