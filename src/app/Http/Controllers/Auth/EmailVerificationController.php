<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function verifyEmail(Request $request): RedirectResponse
    {
        $user = User::findOrFail($request->route('id'));
        
        if (!hash_equals((string) $user->getKey(), (string) $request->route('id')) ||
            !hash_equals(sha1($user->getEmailForVerification()), (string) $request->route('hash'))) {
                return redirect()->intended(config('app.frontend_url'));
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return redirect()->intended(config('app.frontend_url'));
    }

    /**
     * Send a new email verification notification.
     */
    public function sendVerificationNotification(Request $request): JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'status' => false,
                'message' => 'User has already been verified'
            ]);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json([
            'status' => true,
            'message' => 'Verification link sent'
        ]);
    }
}
