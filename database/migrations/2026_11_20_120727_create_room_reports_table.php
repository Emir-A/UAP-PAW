<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('room', 50);
            $table->string('category', 30);
            $table->string('title', 150);
            $table->text('description');
            $table->string('photo_path')->nullable();
            $table->string('status', 20)->default('baru');
            $table->timestamps();
            $table->index('room');
            $table->index('status');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('room_reports');
    }
};
