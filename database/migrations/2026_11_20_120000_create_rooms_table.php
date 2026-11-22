<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('rooms')) {
            Schema::create('rooms', function (Blueprint $table) {
                $table->string('code')->primary();
                $table->string('building');
                $table->unsignedTinyInteger('floor')->nullable();
                $table->unsignedInteger('capacity')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};