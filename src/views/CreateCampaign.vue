<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="md:grid md:grid-cols-3 md:gap-6">
      <div class="md:col-span-1">
        <div class="px-4 sm:px-0">
          <h3 class="text-lg font-medium leading-6 text-gray-900">Create Campaign</h3>
          <p class="mt-1 text-sm text-gray-600">
            Start a new fundraising campaign for a cause you believe in.
          </p>
        </div>
      </div>
      <div class="mt-5 md:mt-0 md:col-span-2">
        <form @submit.prevent="handleSubmit">
          <div class="shadow sm:rounded-md sm:overflow-hidden">
            <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
              <div>
                <label for="title" class="block text-sm font-medium text-gray-700">
                  Campaign Title
                </label>
                <div class="mt-1">
                  <input
                    type="text"
                    name="title"
                    id="title"
                    v-model="form.title"
                    required
                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                  />
                </div>
              </div>

              <div>
                <label for="description" class="block text-sm font-medium text-gray-700">
                  Description
                </label>
                <div class="mt-1">
                  <textarea
                    id="description"
                    name="description"
                    rows="3"
                    v-model="form.description"
                    required
                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                  ></textarea>
                </div>
              </div>

              <div>
                <label for="target_amount" class="block text-sm font-medium text-gray-700">
                  Target Amount ($)
                </label>
                <div class="mt-1">
                  <input
                    type="number"
                    name="target_amount"
                    id="target_amount"
                    v-model="form.target_amount"
                    required
                    min="1"
                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                  />
                </div>
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label for="start_date" class="block text-sm font-medium text-gray-700">
                    Start Date
                  </label>
                  <div class="mt-1">
                    <input
                      type="date"
                      name="start_date"
                      id="start_date"
                      v-model="form.start_date"
                      required
                      class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                    />
                  </div>
                </div>

                <div>
                  <label for="end_date" class="block text-sm font-medium text-gray-700">
                    End Date
                  </label>
                  <div class="mt-1">
                    <input
                      type="date"
                      name="end_date"
                      id="end_date"
                      v-model="form.end_date"
                      required
                      class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                    />
                  </div>
                </div>
              </div>
            </div>
            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
              <button
                type="submit"
                :disabled="loading"
                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
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
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useCampaignStore } from '@/stores/campaigns';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const campaignStore = useCampaignStore();
const authStore = useAuthStore();

const loading = ref(false);
const form = ref({
  title: '',
  description: '',
  target_amount: 0,
  start_date: '',
  end_date: '',
});

async function handleSubmit() {
  if (!authStore.user) return;

  try {
    loading.value = true;
    await campaignStore.createCampaign({
      title: form.value.title,
      description: form.value.description,
      target_amount: form.value.target_amount,
      start_date: new Date(form.value.start_date).toISOString(),
      end_date: new Date(form.value.end_date).toISOString(),
      creator_id: authStore.user.id,
      status: 'active'
    });
    router.push('/campaigns');
  } catch (error) {
    console.error('Error creating campaign:', error);
  } finally {
    loading.value = false;
  }
}
</script>