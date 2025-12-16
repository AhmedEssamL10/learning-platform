<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Services\PaymobService;
use App\Models\SubscriptionPlan;

class SubscriptionController extends Controller
{
    protected $paymobService;
    public function __construct(PaymobService $paymobService)
    {
        $this->paymobService = $paymobService;
    }
    public function subscribe(Request $request)
    {
        $user = auth()->user();
        $planId = $request->input('plan_id');
        $token = $this->paymobService->authenticate()->getContent();
        if (!$token) {
            return response()->json(['error' => 'Payment gateway authentication failed'], 500);
        }
        if ($user->activeSubscription()) {
            return response()->json(['error' => 'You already have an active subscription.'], 400);
        }
        $plan = SubscriptionPlan::find($planId);

        $order = $this->paymobService->createOrder($token, $plan->price);
        if (!$order) {
            return response()->json(['error' => 'Order creation failed'], 500);
        }
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $planId,
            'start_date' => now(),
            'end_date' => now()->addDays($plan->duration),
            'status' => 'pending',
            'payment_reference' => $order['id'],
        ]);
        return response()->json(['message' => 'Subscription successful', 'url' => $order['url']]);
    }
    public function paymentCallback(Request $request)
    {
        $payload = $request->all();
        if ($this->paymobService->verifyCallback($payload)) {
            $subscriptionId = $payload['id'];
            $status = $payload['status'];
            $subscription = Subscription::where('payment_reference', $subscriptionId)->first();
            if ($subscription) {
                if ($status === 'success') {
                    $subscription->status = 'active';
                    $subscription->save();
                } else {
                    $subscription->delete();
                }
            }
            return response()->json(['message' => 'Callback processed'], 200);
        } else {
            return response()->json(['error' => 'Invalid callback'], 400);
        }
    }
}