<?php

namespace App\Http\Controllers;

use App\Models\SellerRequest;
use App\Models\User;
use App\Notifications\NewSellerRequestNotification;
use App\Notifications\SellerRequestStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerRequestController extends Controller
{
    public function requestSellerRole()
    {
        $user = Auth::user();

        // Check if there is a previous request
        if (SellerRequest::where('buyer_id', $user->id)->where('status', 'pending')->exists()) {
            return response()->json(['message' => 'You have an application already under review.'], 400);
        }

        // Create a new request
        $request = SellerRequest::create([
            'buyer_id' => $user->id,
            'status' => 'pending'
        ]);
        $admin = User::role('admin')->first();
        $admin->notify(new NewSellerRequestNotification($request));
        return response()->json(['message' => 'Request sent successfully, it will be reviewed soon.', 'request' => $request], 201);
    }
    public function getAllRequests()
    {
        $requests = SellerRequest::with('buyer')->where('status', 'pending')->get();
        return response()->json($requests);
    }
    public function approveOrReject(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'nullable|string'
        ]);

        $sellerRequest = SellerRequest::findOrFail($id);
        $sellerRequest->status = $request->status;
        $sellerRequest->rejection_reason = $request->status === 'rejected' ? $request->rejection_reason : null;
        $sellerRequest->save();

        // Send notification to the buyer
        $sellerRequest->buyer->notify(new SellerRequestStatusNotification($request->status, $request->rejection_reason));

        return response()->json(['message' => 'Request status updated successfully']);
    }
}
