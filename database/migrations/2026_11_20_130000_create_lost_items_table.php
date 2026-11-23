<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lost_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('item_name', 120);
            $table->text('description');
            $table->string('location', 100);
            $table->dateTime('lost_at');
            $table->string('photo_path')->nullable();
            $table->string('status', 20)->default('hilang'); // 'hilang' atau 'ditemukan'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lost_items');
    }
};