<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function currentPlan(Request $request)
    {
        $subscription = Subscription::with('plan')
            ->where('client_id', $request->user()->id)
            ->where('status', 'active')
            ->latest()
            ->first();

        if (!$subscription) {
            return response()->json([
                'plan' => null
            ]);
        }

        return response()->json([
            'plan' => [
                'name' => $subscription->plan->name,
                'ends_at' => $subscription->ends_at,
            ]
        ]);
    }
}
