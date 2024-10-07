<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;


class UserController extends BaseController
{
    use AuthorizesRequests;

    public function __construct()
    { //third way in Authorization (easiest)
        $this->authorizeResource(User::class, 'user');
    }
    /**
     * Display a listing of the resource (Home Page for authenticated facilitator).
     */
    public function index()
    {
        $facilitators = User::where('type', 'facilitator')->get();

        return Inertia::render('User/Index', [
            'facilitator_accs' => $facilitators,
        ]);
    }

    /**
     * Show the form for creating a new facilitator account.
     */
    public function create(Request $request)
    {
        if ($request->user()->type == "facilitator") {
            return abort(403, 'Unauthorized action.');
        }
        return inertia('User/Create');
    }

    /**
     * Store a newly created facilitator account in storage.
     */
    public function store(Request $request)
    {
        // Validate the request first
        $validated = $request->validate([
            'lname' => 'required|string|max:255',
            'fname' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create the user
        User::create([
            'type' => 'facilitator', //type to facilitator
            'lname' => $validated['lname'],
            'fname' => $validated['fname'],
            'birth_date' => $validated['birth_date'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'program' => null,  // Default to null for user type facilitator
            'valid_id' => null // Default to null for user type facilitator
        ]);

        return redirect()->route('homepage')->with('success', 'User account created successfully.');
    }


    /**
     * Display the specified facilitator.
     */
    public function show(User $facilitator)
    {
        return Inertia::render('Users/Show', [
            'facilitator' => $facilitator,
        ]);
    }

    /**
     * Show the form for editing the specified facilitator.
     */
    public function edit(User $facilitator)
    {
        return Inertia::render('Users/Edit', [
            'facilitator' => $facilitator,
        ]);
    }

    /**
     * Update the specified facilitator in storage.
     */
    public function update(Request $request, User $facilitator)
    {
        $validated = $request->validate([
            'lname' => 'required|string|max:255',
            'fname' => 'required|string|max:255',
            'school_id_number' => 'nullable|string|unique:users,school_id_number,' . $facilitator->id,
            'program' => 'nullable|string|max:255',
            'birth_date' => 'required|date',
            'email' => 'required|string|email|max:255|unique:users,email,' . $facilitator->id,
            'password' => 'nullable|string|min:8|confirmed',
            'valid_id' => 'nullable|string',
        ]);

        $facilitator->update([
            'lname' => $validated['lname'],
            'fname' => $validated['fname'],
            'school_id_number' => $validated['school_id_number'],
            'program' => $validated['program'],
            'birth_date' => $validated['birth_date'],
            'email' => $validated['email'],
            'password' => $validated['password'] ? Hash::make($validated['password']) : $facilitator->password,
            'valid_id' => $validated['valid_id'],
        ]);

        return redirect()->route('users.index')->with('success', 'User account updated successfully.');
    }
    public function updateAccStatus(User $user, Request $request)
    {
        // Get the current status of the user
        $currStatus = $user->acc_status;

        // Determine the new status based on the current status
        $newStatus = $currStatus === 'active' ? 'disabled' : 'active';

        // Update user with the new status
        $result = $user->update([
            'acc_status' => $newStatus // Ensure this value matches the enum definition
        ]);

        // Optional: Check if the update was successful
        if (!$result) {
            return redirect()->route('users.index')->with('error', "Failed to update the user status.");
        }

        // Redirect with a success message
        return redirect()->route('users.index')->with('success', "Updated successfully, account status has been updated");
    }


    /**
     * Remove the specified facilitator from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User account deleted successfully.');
    }
}
