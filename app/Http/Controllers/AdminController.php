<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;

class AdminController extends Controller
{
    public function users()
    {
        $users = User::with(
            'subscription'
        )->get();

        return UserResource::collection($users);
    }
    public function deactivate($id)
    {
        $subscription = Subscription::findOrFail($id);

        $subscription->update([
            'status' => 'cancelled',
            'end_date' => now(),
        ]);

        return response()->json([
            'message' => 'Subscription deactivated successfully'
        ]);
    }
}