<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="sm:flex sm:items-center">
      <div class="sm:flex-auto">
        <h1 class="text-2xl font-semibold text-gray-900">Active Campaigns</h1>
        <p class="mt-2 text-sm text-gray-700">
          Browse and support meaningful causes within ACME Corp.
        </p>
      </div>
      <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
        <router-link
          to="/campaigns/new"
          class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto"
        >
          Create Campaign
        </router-link>
      </div>
    </div>
    <div class="mt-8 flow-root">
      <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
          <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-300">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                    Title
                  </th>
                  <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                    Progress
                  </th>
                  <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                    End Date
                  </th>
                  <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                    <span class="sr-only">Actions</span>
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 bg-white">
                <tr v-for="campaign in campaignStore.campaigns" :key="campaign.id">
                  <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6">
                    <div class="font-medium text-gray-900">{{ campaign.title }}</div>
                    <div class="text-gray-500">{{ campaign.description }}</div>
                  </td>
                  <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                      <div
                        class="bg-indigo-600 h-2.5 rounded-full"
                        :style="{ width: `${(campaign.current_amount / campaign.target_amount) * 100}%` }"
                      ></div>
                    </div>
                    <div class="mt-1">
                      ${{ campaign.current_amount }} of ${{ campaign.target_amount }}
                    </div>
                  </td>
                  <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                    {{ new Date(campaign.end_date).toLocaleDateString() }}
                  </td>
                  <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                    <button
                      @click="showDonateModal(campaign)"
                      class="text-indigo-600 hover:text-indigo-900"
                    >
                      Donate
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <DonationModal 
      :is-open="isDonationModalOpen" 
      :campaign="selectedCampaign" 
      @close="closeDonateModal"
      @donated="handleDonationSuccess"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useCampaignStore } from '@/stores/campaigns';
import DonationModal from '@/components/DonationModal.vue';
import type { Campaign } from '@/types';

const campaignStore = useCampaignStore();
const isDonationModalOpen = ref(false);
const selectedCampaign = ref<Campaign | null>(null);

onMounted(() => {
  campaignStore.fetchCampaigns();
});

function showDonateModal(campaign: Campaign) {
  selectedCampaign.value = campaign;
  isDonationModalOpen.value = true;
}

function closeDonateModal() {
  isDonationModalOpen.value = false;
  selectedCampaign.value = null;
}

function handleDonationSuccess() {
  // Refresh campaign data after successful donation
  campaignStore.fetchCampaigns();
}
</script>