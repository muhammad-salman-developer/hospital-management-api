<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    public function verify(Request $request, $id, $hash): RedirectResponse
    {
        $user = User::findOrFail($id);

        // Hash match karein (Laravel ka apna tareeqa hai email verify karne ka)
        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return redirect(env('FRONTEND_URL').'/email-verified?status=invalid');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect(env('FRONTEND_URL').'/email-verified?status=already-verified');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect(env('FRONTEND_URL').'/email-verified?status=success');
    }
}
