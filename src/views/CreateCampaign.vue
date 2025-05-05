<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="md:grid md:grid-cols-3 md:gap-6">
      <div class="md:col-span-1">
        <div class="px-4 sm:px-0">
          <h3 class="text-lg font-medium leading-6 text-gray-900">Create New Campaign</h3>
          <p class="mt-1 text-sm text-gray-600">
            Fill out the form to create a new donation campaign.
          </p>
        </div>
      </div>
      <div class="mt-5 md:col-span-2 md:mt-0">
        <form @submit.prevent="submitForm">
          <div class="shadow sm:overflow-hidden sm:rounded-md">
            <div class="space-y-6 bg-white px-4 py-5 sm:p-6">
              <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Campaign Title</label>
                <input
                  type="text"
                  id="title"
                  v-model="form.title"
                  required
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>

              <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea
                  id="description"
                  v-model="form.description"
                  rows="4"
                  required
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                ></textarea>
              </div>

              <div>
                <label for="goal" class="block text-sm font-medium text-gray-700">Goal Amount ($)</label>
                <input
                  type="number"
                  id="goal"
                  v-model="form.goalAmount"
                  min="100"
                  required
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>

              <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                <input
                  type="date"
                  id="end_date"
                  v-model="form.endDate"
                  required
                  :min="minDate"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>

              <div>
                <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                <select
                  id="category"
                  v-model="form.category"
                  required
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                  <option value="">Select a category</option>
                  <option value="Environmental">Environmental</option>
                  <option value="Social">Social</option>
                  <option value="Educational">Educational</option>
                  <option value="Health">Health</option>
                  <option value="Technology">Technology</option>
                </select>
              </div>

              <div>
                <label for="image_url" class="block text-sm font-medium text-gray-700">Image URL</label>
                <input
                  type="url"
                  id="image_url"
                  v-model="form.imageUrl"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
              <button
                type="button"
                @click="$router.push('/campaigns')"
                class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 mr-3"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="loading"
                class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
              >
                {{ loading ? 'Creating...' : 'Create Campaign' }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { supabase } from '@/lib/supabaseClient';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const authStore = useAuthStore();
const loading = ref(false);

const form = ref({
  title: '',
  description: '',
  goalAmount: 1000,
  endDate: '',
  category: '',
  imageUrl: 'https://source.unsplash.com/random/800x600/?charity'
});

const minDate = computed(() => {
  const today = new Date();
  today.setDate(today.getDate() + 1); // Minimum end date is tomorrow
  return today.toISOString().split('T')[0];
});

async function submitForm() {
  if (!authStore.user) return;
  
  loading.value = true;
  
  try {
    const { data, error } = await supabase.from('campaigns').insert({
      title: form.value.title,
      description: form.value.description,
      goal_amount: form.value.goalAmount,
      end_date: form.value.endDate,
      category: form.value.category,
      image_url: form.value.imageUrl,
      creator_id: authStore.user.id,
      status: 'active'
    });
    
    if (error) throw error;
    
    router.push('/campaigns');
  } catch (error) {
    console.error('Error creating campaign:', error);
    alert('Failed to create campaign. Please try again.');
  } finally {
    loading.value = false;
  }
}
</script>