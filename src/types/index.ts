export interface User {
  id: string;
  email: string;
  full_name: string;
  role: 'employee' | 'admin';
  created_at: string;
}

export interface Campaign {
  id: string;
  title: string;
  description: string;
  target_amount: number;
  current_amount: number;
  start_date: string;
  end_date: string;
  creator_id: string;
  status: 'active' | 'completed' | 'cancelled';
  created_at: string;
}

export interface Donation {
  id: string;
  campaign_id: string;
  donor_id: string;
  amount: number;
  message?: string;
  created_at: string;
}