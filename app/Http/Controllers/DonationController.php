<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Donation;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function store(Request $request, Campaign $campaign)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'message' => 'nullable|string|max:500',
        ]);

        // Process payment (placeholder for now)
        $paymentResult = $this->paymentService->processPayment($request->amount, [
            'user_id' => $request->user()->id,
            'campaign_id' => $campaign->id,
        ]);

        // Create donation record
        $donation = Donation::create([
            'amount' => $request->amount,
            'message' => $request->message,
            'campaign_id' => $campaign->id,
            'donor_id' => $request->user()->id,
            'payment_id' => $paymentResult['payment_id'],
            'status' => $paymentResult['status'],
        ]);

        return response()->json([
            'donation' => $donation,
            'message' => 'Thank you for your donation!',
        ], 201);
    }

    public function userDonations(Request $request)
    {
        $donations = Donation::with('campaign')
            ->where('donor_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($donations);
    }
}