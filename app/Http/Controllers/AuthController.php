<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Import for password hashing
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function create()
    {
        return inertia('Auth/Login');
    }

    public function store(Request $request)
    {
        // Validate credentials
        $credentials = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        // Attempt authentication
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => 'Authentication Failed',
            ]);
        }

        // Check if the authenticated user's account status is 'disabled'
        if (Auth::user()->acc_status === 'disabled') {
            Auth::logout();
            return abort(403, "Your account is disabled, Contact admin");
        }

        // Regenerate session ID after successful login
        $request->session()->regenerate();
        $userFullName = Auth::user()->fname . " " . Auth::user()->middle_initial . " " . Auth::user()->lname;
        // Redirect based on user type
        if (Auth::user()->type === 'admin') {
            return redirect()->route('admins.index')->with('success', "Welcome Administrator $userFullName!");
        } elseif (Auth::user()->type === 'facilitator') {
            return redirect()->route('facilitators.index')->with('success', "Welcome Facilitator $userFullName!");
        }

        // Fallback to homepage if no user type matches
        return redirect()->intended(route('homepage'))->with('success', "Log In Success!");
    }


    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('homepage');
    }

    // Add the updatePassword function
    public function updatePassword(Request $request)
    {
        // Validate the form inputs
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed', // new_password_confirmation required by 'confirmed'
        ]);

        // Check if the current password matches
        if (!Hash::check($request->current_password, Auth::user()->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'The current password is incorrect.',
            ]);
        }

        // Update the user's password
        Auth::user()->update([
            'password' => Hash::make($request->new_password)
        ]);

        // Regenerate session after password change for security
        $request->session()->regenerate();

        return redirect()->back()->with('success', 'Your password has been successfully updated!');
    }
}
