import { defineStore } from 'pinia';
import { ref } from 'vue';
import { campaigns } from '@/lib/api';
import type { Campaign } from '@/types';

export const useCampaignStore = defineStore('campaigns', () => {
  const campaignsList = ref<Campaign[]>([]);
  const loading = ref(false);

  async function fetchCampaigns() {
    try {
      loading.value = true;
      const { data } = await campaigns.list();
      campaignsList.value = data;
    } catch (error) {
      console.error('Error fetching campaigns:', error);
      throw error;
    } finally {
      loading.value = false;
    }
  }

  async function createCampaign(campaign: Omit<Campaign, 'id' | 'created_at' | 'current_amount'>) {
    try {
      loading.value = true;
      const { data } = await campaigns.create(campaign);
      campaignsList.value.unshift(data);
    } catch (error) {
      console.error('Error creating campaign:', error);
      throw error;
    } finally {
      loading.value = false;
    }
  }

  return {
    campaigns: campaignsList,
    loading,
    fetchCampaigns,
    createCampaign
  };
});