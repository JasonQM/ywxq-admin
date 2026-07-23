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
        Schema::create('player_action_logs', function (Blueprint $table) {
            $table->id();
            $table->string('uid', 32)->index();
            $table->string('action', 20)->index();
            $table->text('remark');
            $table->timestamp('operated_at')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_action_logs');
    }
};
