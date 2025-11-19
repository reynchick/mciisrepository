<?php


namespace App\Http\Controllers;


use App\Models\User;
use App\Models\Faculty;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Inertia\Inertia;


class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!$request->user() || !$request->user()->isAdministrator()) {
                abort(403, 'Unauthorized action.');
            }
            return $next($request);
        });
    }


    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $users = User::with('roles')
            ->when(request('search'), function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('student_id', 'like', "%{$search}%")
                      ->orWhere('faculty_id', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);


        // User role distribution for pie chart
        $roleDistribution = User::with('roles')
            ->get()
            ->flatMap(fn($user) => $user->roles)
            ->groupBy('name')
            ->map(fn($group) => $group->count())
            ->map(fn($count, $name) => [
                'role' => $name,
                'count' => $count,
            ])
            ->values();


        // Recent user registrations (last 30 days)
        $recentRegistrations = User::where('created_at', '>=', now()->subDays(30))->count();


        return Inertia::render('User/Index', [
            'users' => $users,
            'filters' => request()->only(['search']),
            'roleDistribution' => $roleDistribution,
            'recentRegistrations' => $recentRegistrations,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $roles = \App\Models\Role::all();
       
        return Inertia::render('User/Create', [
            'roles' => $roles
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * Pre-register a user so when they sign in with Google SSO for the first time,
     * the system will update their existing record with the correct roles and IDs.
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
       
        // Extract role_ids before creating user
        $roleIds = $userData['role_ids'];
        unset($userData['role_ids']);
       
        // Pre-register user (they will sign in via Google SSO)
        $user = User::create([
            ...$userData,
            'password' => null, // No password - Google SSO only
            'email_verified_at' => null, // Will be verified on first Google sign-in
        ]);


        // Attach the roles
        $user->roles()->attach($roleIds);


        return redirect()->route('users.index')
            ->with('success', 'User pre-registered successfully. They can now sign in with Google using their USeP email.');
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): Response
    {
        $roles = \App\Models\Role::all();
       
        return Inertia::render('User/Edit', [
            'user' => $user->load('roles'),
            'roles' => $roles
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $userData = $request->validated();
       
        // Extract role_ids before updating user
        $roleIds = $userData['role_ids'];
        unset($userData['role_ids']);
       
        $user->update($userData);
       
        // Sync the roles
        $user->roles()->sync($roleIds);
       
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