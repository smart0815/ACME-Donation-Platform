<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();
        
        // Get active campaigns count
        $activeCampaigns = Campaign::active()->count();
        
        // Get total donations
        $totalDonations = Donation::sum('amount');
        
        // Get user contributions if authenticated
        $userContributions = 0;
        if ($user) {
            $userContributions = Donation::where('donor_id', $user->id)->sum('amount');
        }
        
        return response()->json([
            'activeCampaigns' => $activeCampaigns,
            'totalDonations' => $totalDonations,
            'userContributions' => $userContributions,
        ]);
    }
    
    public function userContributions(Request $request)
    {
        $user = $request->user();
        
        $donations = Donation::with('campaign')
            ->where('donor_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        $totalContributed = $donations->sum('amount');
        $campaignsSupported = $donations->pluck('campaign_id')->unique()->count();
        
        return response()->json([
            'totalContributed' => $totalContributed,
            'campaignsSupported' => $campaignsSupported,
            'recentDonations' => $donations->take(5),
        ]);
    }
    
    public function adminStats(Request $request)
    {
        // Ensure user is admin
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        // Get user count
        $totalUsers = User::count();
        
        // Get campaign count
        $totalCampaigns = Campaign::count();
        
        // Get donation sum
        $totalDonations = Donation::sum('amount');
        
        // Get monthly donations for the last 6 months
        $monthlyDonations = Donation::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(amount) as total')
        )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get();
            
        // Get recent activity
        $recentDonations = Donation::with(['donor', 'campaign'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        $recentCampaigns = Campaign::with('creator')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        return response()->json([
            'totalUsers' => $totalUsers,
            'totalCampaigns' => $totalCampaigns,
            'totalDonations' => $totalDonations,
            'monthlyDonations' => $monthlyDonations,
            'recentDonations' => $recentDonations,
            'recentCampaigns' => $recentCampaigns,
        ]);
    }
}