<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('title');
            $table->string('program');
            $table->string('class_group');
            $table->text('lecturer');
            $table->string('room');
            $table->unsignedTinyInteger('weekday');
            $table->string('start_time', 5);
            $table->string('end_time', 5);
        });

        Schema::create('timetable_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('course_session_id');
            $table->foreign('course_session_id')
                ->references('id')
                ->on('course_sessions')
                ->restrictOnDelete();

            $table->timestamps();
            $table->unique(['user_id', 'course_session_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timetable_entries');
        Schema::dropIfExists('course_sessions');
    }
};