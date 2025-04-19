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
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->string('medication_name');
            $table->foreignId('doctor_id')->constrained()->onDelete('cascade');
            $table->foreignId('patient_record_id')->constrained()->onDelete('cascade');
            $table->string('dosage_amount')->nullable();
            $table->string('frequency')->nullable();
            $table->string('duration');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
