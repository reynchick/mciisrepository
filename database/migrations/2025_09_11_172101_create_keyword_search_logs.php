<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keyword_search_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('keyword_id')
                ->constrained('keywords')
                ->cascadeOnDelete()
                ->cascadeOnUpdate()
                ->index();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate()
                ->index();
            $table->timestamps();

            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keyword_search_logs');
    }
};
