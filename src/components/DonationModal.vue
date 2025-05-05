<template>
  <div v-if="isOpen" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-medium text-gray-900">Donate to {{ campaign?.title }}</h3>
        <button @click="close" class="text-gray-400 hover:text-gray-500">
          <span class="sr-only">Close</span>
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      
      <form @submit.prevent="handleSubmit">
        <div class="mb-4">
          <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
          <div class="mt-1 relative rounded-md shadow-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <span class="text-gray-500 sm:text-sm">$</span>
            </div>
            <input
              type="number"
              id="amount"
              v-model="form.amount"
              min="1"
              step="0.01"
              required
              class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md"
              placeholder="0.00"
            />
          </div>
        </div>
        
        <div class="mb-4">
          <label for="message" class="block text-sm font-medium text-gray-700">Message (optional)</label>
          <div class="mt-1">
            <textarea
              id="message"
              v-model="form.message"
              rows="3"
              class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
            ></textarea>
          </div>
        </div>
        
        <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
          <button
            type="submit"
            :disabled="loading"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:col-start-2 sm:text-sm"
          >
            {{ loading ? 'Processing...' : 'Donate' }}
          </button>
          <button
            type="button"
            @click="close"
            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm"
          >
            Cancel
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { campaigns } from '@/lib/api';
import type { Campaign } from '@/types';

const props = defineProps<{
  isOpen: boolean;
  campaign: Campaign | null;
}>();

const emit = defineEmits(['close', 'donated']);
const loading = ref(false);

const form = ref({
  amount: 0,
  message: ''
});

async function handleSubmit() {
  if (!props.campaign) return;
  
  try {
    loading.value = true;
    await campaigns.donate(props.campaign.id, {
      amount: form.value.amount,
      message: form.value.message
    });
    
    emit('donated', {
      campaignId: props.campaign.id,
      amount: form.value.amount
    });
    close();
  } catch (error) {
    console.error('Error making donation:', error);
  } finally {
    loading.value = false;
  }
}

function close() {
  form.value = { amount: 0, message: '' };
  emit('close');
}
</script>