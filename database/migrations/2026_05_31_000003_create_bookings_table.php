<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('slot', 1);
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('customer_name');
            $table->string('plate_number');
            $table->string('vehicle_type');
            $table->string('vehicle_model');
            $table->unsignedInteger('total_price');
            $table->unsignedInteger('total_duration_minutes');
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();

            $table->index(['slot', 'start_time', 'end_time']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
