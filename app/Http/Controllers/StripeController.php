<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\Payment;
use App\Models\Plan;
use Illuminate\Http\Request;
use Laravel\Cashier\Cashier;

class StripeController extends Controller
{
    public function checkout(Request $request)
    {
        $request->validate([
            'plan_id' => ['required', 'exists:plans,id'],
        ]);

        $user = $request->user();
        $plan = Plan::findOrFail($request->plan_id);

        if (! $plan->stripe_price_id) {
            return back()->with('error', 'Online payment is not available for this plan yet. Please pay at the desk.');
        }

        $checkout = $user->newSubscription('default', $plan->stripe_price_id)
            ->checkout([
                'success_url' => route('stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('member.dashboard'),
            ]);

        return redirect($checkout->url);
    }

    public function success(Request $request)
    {
        return redirect()->route('member.dashboard')->with('success', 'Payment successful! Your membership is now active.');
    }

    public function webhook()
    {
        // Handled automatically by Cashier's built-in webhook handler
        // Additional logic in App\Listeners\StripeWebhookListener
    }
}
