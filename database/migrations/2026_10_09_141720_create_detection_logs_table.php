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
        Schema::create('detection_logs', function (Blueprint $table) {
            $table->id();
            $table->string('category', 32); // 'object' or 'behavior'
            $table->string('label', 64);
            $table->string('display_name', 128);
            $table->float('confidence')->default(1.0);
            $table->string('color', 32)->default('#3B82F6');
            $table->json('details')->nullable();
            $table->timestamps();

            $table->index(['category', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detection_logs');
    }
};
