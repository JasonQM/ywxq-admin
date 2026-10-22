<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_statistics', function (Blueprint $table): void {
            $table->dropColumn('real_name_cost');
        });
    }

    public function down(): void
    {
        Schema::table('daily_statistics', function (Blueprint $table): void {
            $table->decimal('real_name_cost', 14, 2)
                ->default(0)
                ->after('real_name_num');
        });
    }
};
