<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Faculty;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Inertia\Response;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $users = User::with('role')
            ->when(request('search'), function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->paginate(15);

        return Inertia::render('Users/Index', [
            'users' => $users,
            'filters' => request()->only(['search'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Users/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $userData = $request->validated();
        
        // Validate that faculty_id and email exists in faculty table if faculty_id is provided
        if (!empty($userData['faculty_id'])) {
            $faculty = Faculty::where('faculty_id', $userData['faculty_id'])
                ->where('email', $userData['email'])
                ->first();
            if (!$faculty) {
                return back()->withErrors([
                    'faculty_id' => 'This Faculty ID and email combination is not registered in our faculty database.',
                    'email' => 'This Faculty ID and email combination is not registered in our faculty database.'
                ]);
            }
        }
        
        // Create user with admin-provided password
        $user = User::create([
            ...$userData,
            'password' => Hash::make($userData['password']),
        ]);

        // If creating admin or staff user, mark as needing password change
        if ($user->isAdminOrStaff()) {
            $user->update([
                'must_change_password' => true,
                'is_temporary_password' => true,
                'password_changed_at' => null,
            ]);
            
            return redirect()->route('users.index')
                ->with('success', 'User created successfully. They will be prompted to change their password on first login.');
        }
        
        return redirect()->route('users.index')
            ->with('success', 'User created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): Response
    {
        return Inertia::render('Users/Edit', [
            'user' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $user->update($request->validated());
        
        return redirect()->route('users.index')
            ->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        $user->delete();
        
        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully');
    }
}