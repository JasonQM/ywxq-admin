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
        Schema::create('data_alerts', function (Blueprint $table) {
            $table->id();
            $table->date('day')->index();
            $table->string('level', 20)->index();
            $table->string('type', 80)->index();
            $table->string('title');
            $table->text('message');
            $table->string('status', 20)->default('open')->index();
            $table->timestamps();

            $table->unique(['day', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_alerts');
    }
};
