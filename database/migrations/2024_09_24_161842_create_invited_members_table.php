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
        Schema::create('invited_members', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('email'); // L'email doit être unique
            // $table->string('name');
            $table->foreignId('groupe_id')->constrained('groupes')->onDelete('cascade'); // Lien avec le groupe
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invited_members');
    }
};
