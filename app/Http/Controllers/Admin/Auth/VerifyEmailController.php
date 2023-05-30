<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Models\AdminUser;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Foundation\Auth\EmailVerificationRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(Request $request)
    {
        $adminUser = AdminUser::where('id', $request->route('id'))->first();
        if ($adminUser->hasVerifiedEmail()) {
            return redirect()->intended('/admin/dashboard?verified=1');
        }

        if ($adminUser->markEmailAsVerified()) {
            event(new Verified($adminUser));
        }
        return redirect()->route('admin.login')->with('success', 'User has been verified successfully.');
        // return redirect()->intended('/admin/dashboard?verified=1');
    }
}
