<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $query = Campaign::with('creator');

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Search by title or description
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort by created_at by default, or by the provided sort field
        $sortField = $request->sort_by ?? 'created_at';
        $sortDirection = $request->sort_direction ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        $campaigns = $query->paginate(10);

        return response()->json($campaigns);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'goal_amount' => 'required|numeric|min:1',
            'end_date' => 'required|date|after:today',
            'image_url' => 'nullable|url',
        ]);

        $campaign = Campaign::create([
            'title' => $request->title,
            'description' => $request->description,
            'goal_amount' => $request->goal_amount,
            'end_date' => $request->end_date,
            'image_url' => $request->image_url,
            'status' => 'active',
            'creator_id' => $request->user()->id,
        ]);

        return response()->json($campaign, 201);
    }

    public function show(Campaign $campaign)
    {
        $campaign->load(['creator', 'donations.donor']);
        
        return response()->json($campaign);
    }

    public function update(Request $request, Campaign $campaign)
    {
        // Check if user is the creator or an admin
        if ($request->user()->id !== $campaign->creator_id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'goal_amount' => 'sometimes|numeric|min:1',
            'end_date' => 'sometimes|date|after:today',
            'image_url' => 'sometimes|nullable|url',
            'status' => 'sometimes|in:active,paused,completed',
        ]);

        $campaign->update($request->all());

        return response()->json($campaign);
    }

    public function destroy(Request $request, Campaign $campaign)
    {
        // Check if user is the creator or an admin
        if ($request->user()->id !== $campaign->creator_id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $campaign->delete();

        return response()->json(['message' => 'Campaign deleted successfully']);
    }
}