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
        Schema::create('scheduled_preset_commands', function (Blueprint $table) {
            $table->id();
            $table->string('batch_id', 36)->index();
            $table->unsignedBigInteger('device_id')->index();
            $table->unsignedBigInteger('preset_id');
            $table->unsignedInteger('command_index');
            $table->json('command_data');
            $table->timestamp('execute_at')->index();
            $table->enum('status', ['pending', 'dispatched', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->timestamps();

            $table->index(['status', 'execute_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_preset_commands');
    }
};
