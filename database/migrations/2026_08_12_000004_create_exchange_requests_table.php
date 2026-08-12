<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exchange_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('receiver_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('sender_user_skill_id')->constrained('user_skills')->restrictOnDelete();
            $table->foreignId('receiver_user_skill_id')->constrained('user_skills')->restrictOnDelete();
            $table->string('learning_format');
            $table->string('preferred_schedule');
            $table->text('message');
            $table->string('status')->index();
            $table->timestamp('responded_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->index(['receiver_id', 'status']);
            $table->index(['sender_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exchange_requests');
    }
};
