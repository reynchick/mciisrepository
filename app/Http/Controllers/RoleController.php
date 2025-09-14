<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Role::class, ['index', 'show']);
    }

    /**
     * Display a listing of the roles.
     */
    public function index(): Response
    {
        $roles = Role::query()
            ->withCount('users')
            ->when(request('search'), fn($query, $search) => 
                $query->search($search)
            )
            ->paginate();

        return Inertia::render('Role/Index', [
            'roles' => $roles,
            'filters' => request()->only(['search'])
        ]);
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role): Response
    {
        $role->load(['users' => function ($query) {
            $query->latest();
        }]);

        return Inertia::render('Role/Show', [
            'role' => $role
        ]);
    }
}