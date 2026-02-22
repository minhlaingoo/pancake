<?php

use App\Models\Device;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('device_components', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Device::class)->constrained()->onDelete('cascade');
            $table->string('type');
            $table->string('unit')->nullable();
            $table->string('name')->nullable();
            $table->string('last_value')->nullable();
            $table->string('status')->default('unknown');
            $table->boolean('is_sensor')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_components');
    }
};
