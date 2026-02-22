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
        Schema::create('ping_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ping_target_id')->constrained('ping_targets')->cascadeOnDelete();
            $table->float('latency', 10, 3)->nullable();
            $table->boolean('is_reachable');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['ping_target_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ping_results');
    }
};
