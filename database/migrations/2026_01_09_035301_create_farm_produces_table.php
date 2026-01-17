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
        // database/migrations/xxxx_xx_xx_create_farm_produces_table.php
        Schema::create('farm_produces', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->bigIncrements('id');
            $table->unsignedBigInteger('farmer_id');

            $table->string('product');
            $table->string('description')->nullable();
            $table->integer('quantity');
            $table->integer('price');
            $table->string('image')->nullable();

            /*
            |--------------------------------------------------------------------------
            | STATUS FLOW
            |--------------------------------------------------------------------------
            | draft        → just created, not visible
            | available    → visible & can be preordered
            | pending      → has preorders waiting approval
            | approved     → approved for fulfillment
            | sold_out     → quantity = 0
            | rejected     → disapproved
            */
            $table->string('status')->default('draft');
            $table->integer('reserved_quantity')->default(0);
            $table->timestamps();

            $table->foreign('farmer_id')
                ->references('id')
                ->on('farmers')
                ->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farm_produces');
    }
};
