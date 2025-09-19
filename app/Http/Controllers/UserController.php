<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Inertia\Response;
use Inertia\Inertia;

class UserController extends Controller
{
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
            ->paginate(10);

        return Inertia::render('Users/Index', [
            'users' => $users,
            'filters' => request()->only(['search'])
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Users/Create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $userData = $request->validated();
        
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

    public function edit(User $user): Response
    {
        return Inertia::render('Users/Edit', [
            'user' => $user
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $user->update($request->validated());
        
        return redirect()->route('users.index')
            ->with('success', 'User updated successfully');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();
        
        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully');
    }
}