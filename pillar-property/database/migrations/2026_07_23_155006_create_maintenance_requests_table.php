<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete(); // Nullable if staff creates it
            $table->foreignId('vendor_id')->nullable()->constrained()->nullOnDelete();
            $table->string('category'); // plumbing, electrical, appliance, hvac, other
            $table->text('description');
            $table->string('photo_path')->nullable();
            $table->string('urgency')->default('routine'); // routine, urgent, emergency
            $table->string('status')->default('submitted'); // submitted, assigned, in_progress, resolved
            $table->decimal('cost', 10, 2)->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_requests');
    }
};
