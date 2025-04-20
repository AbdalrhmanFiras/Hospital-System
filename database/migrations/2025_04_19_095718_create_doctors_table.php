<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('Name');
            $table->string('Specialization');
            $table->enum('Degree', ['Bachelor', 'Master', 'Doctoral']);
            $table->enum('Available', ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Saturday']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
