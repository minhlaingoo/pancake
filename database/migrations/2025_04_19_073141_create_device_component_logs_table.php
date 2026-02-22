<?php

use App\Models\Device;
use App\Models\DeviceComponent;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('device_component_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(DeviceComponent::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Device::class)->constrained()->onDelete('cascade');
            $table->text('value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_component_logs');
    }
};