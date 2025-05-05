<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="text-center">
      <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
        Welcome to ACME Corp Donation Platform
      </h2>
      <p class="mt-4 text-lg text-gray-500">
        Join your fellow employees in supporting meaningful causes and making a difference.
      </p>
    </div>

    <div class="mt-12 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
          <div class="text-center">
            <h3 class="text-lg font-medium text-gray-900">Active Campaigns</h3>
            <p class="mt-2 text-3xl font-semibold text-indigo-600">{{ activeCampaigns }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
          <div class="text-center">
            <h3 class="text-lg font-medium text-gray-900">Total Donations</h3>
            <p class="mt-2 text-3xl font-semibold text-indigo-600">${{ totalDonations }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
          <div class="text-center">
            <h3 class="text-lg font-medium text-gray-900">Your Contributions</h3>
            <p class="mt-2 text-3xl font-semibold text-indigo-600">${{ userContributions }}</p>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-12">
      <router-link
        to="/campaigns/new"
        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700"
      >
        Create New Campaign
      </router-link>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { supabase } from '@/lib/supabaseClient';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();
const activeCampaigns = ref(0);
const totalDonations = ref(0);
const userContributions = ref(0);

onMounted(async () => {
  try {
    // Get active campaigns count
    const { count: campaignCount } = await supabase
      .from('campaigns')
      .select('*', { count: 'exact' })
      .eq('status', 'active');
    
    activeCampaigns.value = campaignCount || 0;

    // Get total donations
    const { data: donationsData } = await supabase
      .from('donations')
      .select('amount');
    
    totalDonations.value = donationsData?.reduce((sum, donation) => sum + donation.amount, 0) || 0;

    // Get user contributions
    if (authStore.user) {
      const { data: userDonations } = await supabase
        .from('donations')
        .select('amount')
        .eq('donor_id', authStore.user.id);
      
      userContributions.value = userDonations?.reduce((sum, donation) => sum + donation.amount, 0) || 0;
    }
  } catch (error) {
    console.error('Error fetching dashboard data:', error);
  }
});
</script>