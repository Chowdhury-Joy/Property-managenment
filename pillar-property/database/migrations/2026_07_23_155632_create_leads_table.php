<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('property_address');
            $table->string('property_type'); // single_family, multi_unit, etc.
            $table->string('current_rent')->nullable(); // e.g., "2000", "Not sure"
            $table->text('reason_for_switching')->nullable();
            $table->string('status')->default('new'); // new, contacted, proposal_sent, converted, lost
            $table->text('staff_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
