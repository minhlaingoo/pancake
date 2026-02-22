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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('type')->nullable();
            $table->string('model')->nullable();
            $table->string('mac')->nullable();
            $table->string('ip')->nullable();
            $table->integer('port')->nullable();
            $table->string('online_status')->default('offline');
            $table->string('timezone')->default('utc');
            $table->boolean('is_active')->default(false);
            $table->string('ntp_server')->nullable();
            $table->integer('ntp_interval')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
