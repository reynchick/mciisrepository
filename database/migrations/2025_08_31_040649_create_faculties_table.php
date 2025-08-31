<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('faculties', function (Blueprint $table) {
            $table->id();
            $table->string('facultyID')->unique(); // e.g., 2022-80000
            $table->string('firstName');
            $table->string('middleName')->nullable();
            $table->string('lastName');
            $table->string('position')->nullable();
            $table->string('designation')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('ORCID')->nullable();
            $table->string('contactNumber')->nullable();
            $table->string('educationalAttainment')->nullable();
            $table->text('fieldOfSpecialization')->nullable();
            $table->text('researchInterest')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faculties');
    }
};
