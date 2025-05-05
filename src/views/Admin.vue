<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-semibold text-gray-900">Admin Dashboard</h1>
    
    <div class="mt-8 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <h3 class="text-lg font-medium text-gray-900">Total Users</h3>
          <p class="mt-2 text-3xl font-semibold text-indigo-600">{{ stats.totalUsers }}</p>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <h3 class="text-lg font-medium text-gray-900">Total Campaigns</h3>
          <p class="mt-2 text-3xl font-semibold text-indigo-600">{{ stats.totalCampaigns }}</p>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <h3 class="text-lg font-medium text-gray-900">Total Donations</h3>
          <p class="mt-2 text-3xl font-semibold text-indigo-600">${{ stats.totalDonations }}</p>
        </div>
      </div>
    </div>

    <div class="mt-8">
      <h2 class="text-lg font-medium text-gray-900 mb-4">Recent Activity</h2>
      <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul role="list" class="divide-y divide-gray-200">
          <li v-for="activity in recentActivity" :key="activity.id" class="px-4 py-4 sm:px-6">
            <div class="flex items-center justify-between">
              <p class="text-sm font-medium text-indigo-600 truncate">
                {{ activity.type === 'donation' ? 'New Donation' : 'New Campaign' }}
              </p>
              <div class="ml-2 flex-shrink-0 flex">
                <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                   :class="activity.type === 'donation' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'">
                  {{ activity.type === 'donation' ? `$${activity.amount}` : 'Created' }}
                </p>
              </div>
            </div>
            <div class="mt-2 sm:flex sm:justify-between">
              <div class="sm:flex">
                <p class="flex items-center text-sm text-gray-500">
                  {{ activity.description }}
                </p>
              </div>
              <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                <p>
                  {{ new Date(activity.created_at).toLocaleDateString() }}
                </p>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { supabase } from '@/lib/supabaseClient';

interface Stats {
  totalUsers: number;
  totalCampaigns: number;
  totalDonations: number;
}

interface Activity {
  id: string;
  type: 'donation' | 'campaign';
  description: string;
  amount?: number;
  created_at: string;
}

const stats = ref<Stats>({
  totalUsers: 0,
  totalCampaigns: 0,
  totalDonations: 0
});

const recentActivity = ref<Activity[]>([]);

onMounted(async () => {
  try {
    // Fetch statistics
    const [usersCount, campaignsCount, donationsSum] = await Promise.all([
      supabase.from('users').select('*', { count: 'exact' }),
      supabase.from('campaigns').select('*', { count: 'exact' }),
      supabase.from('donations').select('amount')
    ]);

    stats.value = {
      totalUsers: usersCount.count || 0,
      totalCampaigns: campaignsCount.count || 0,
      totalDonations: donationsSum.data?.reduce((sum, donation) => sum + donation.amount, 0) || 0
    };

    // Fetch recent activity
    const [recentDonations, recentCampaigns] = await Promise.all([
      supabase
        .from('donations')
        .select(`
          id,
          amount,
          created_at,
          campaigns (
            title
          )
        `)
        .order('created_at', { ascending: false })
        .limit(5),
      supabase
        .from('campaigns')
        .select(`
          id,
          title,
          created_at
        `)
        .order('created_at', { ascending: false })
        .limit(5)
    ]);

    const activities: Activity[] = [
      ...(recentDonations.data?.map(d => ({
        id: d.id,
        type: 'donation' as const,
        description: `Donation to ${d.campaigns.title}`,
        amount: d.amount,
        created_at: d.created_at
      })) || []),
      ...(recentCampaigns.data?.map(c => ({
        id: c.id,
        type: 'campaign' as const,
        description: `New campaign: ${c.title}`,
        created_at: c.created_at
      })) || [])
    ];

    recentActivity.value = activities.sort((a, b) => 
      new Date(b.created_at).getTime() - new Date(a.created_at).getTime()
    ).slice(0, 5);
  } catch (error) {
    console.error('Error fetching admin data:', error);
  }
});
</script>