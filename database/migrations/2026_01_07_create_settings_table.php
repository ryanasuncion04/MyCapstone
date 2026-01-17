<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Insert default appearance settings
        DB::table('settings')->insert([
            ['key' => 'theme', 'value' => 'light', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'primary_color', 'value' => '#0ea5e9', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'layout', 'value' => 'comfortable', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
