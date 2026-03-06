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
        Schema::table('presets', function (Blueprint $table) {
            $table->string('version')->default('1.0')->after('name');
            $table->string('author')->nullable()->after('commands');
            $table->string('status')->default('Draft')->after('author');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presets', function (Blueprint $table) {
            $table->dropColumn(['version', 'author', 'status']);
        });
    }
};
