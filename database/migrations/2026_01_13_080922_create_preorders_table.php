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
        Schema::create('preorders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('farm_produce_id')
                ->constrained('farm_produces')
                ->cascadeOnDelete();

            $table->string('customer_name');
            $table->string('customer_contact')->nullable();

            $table->integer('quantity');

            $table->enum('status', [
                'pending',    // waiting for manager approval
                'approved',   // reserved stock
                'rejected',   // declined
                'completed',  // delivered / picked up
                'cancelled'   // customer cancelled
            ])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pre_orders');
    }
};
