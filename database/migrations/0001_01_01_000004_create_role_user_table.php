<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create pivot table for multiple roles
        Schema::create('role_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->timestamps();
            
            // Prevent duplicate role assignments
            $table->unique(['user_id', 'role_id']);
        });
        
        // Migrate existing data from users.role_id to role_user pivot
        DB::table('users')->orderBy('id')->chunk(100, function ($users) {
            foreach ($users as $user) {
                DB::table('role_user')->insert([
                    'user_id' => $user->id,
                    'role_id' => $user->role_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
        
        // Remove old role_id column from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add role_id back to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->constrained('roles')->after('email');
        });
        
        // Migrate first role from pivot back to role_id
        DB::table('role_user')->orderBy('user_id')->chunk(100, function ($roleUsers) {
            foreach ($roleUsers as $roleUser) {
                DB::table('users')
                    ->where('id', $roleUser->user_id)
                    ->whereNull('role_id')
                    ->update(['role_id' => $roleUser->role_id]);
            }
        });
        
        // Drop pivot table
        Schema::dropIfExists('role_user');
    }
};