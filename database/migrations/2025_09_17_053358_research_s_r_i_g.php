<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('research_srig', function (Blueprint $table) {
            $table->foreignId('research_id')->constrained()->onDelete('cascade');
            $table->foreignId('srig_id')->constrained('srigs')->onDelete('cascade');
            $table->primary(['research_id', 'srig_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('research_srig');
    }
};