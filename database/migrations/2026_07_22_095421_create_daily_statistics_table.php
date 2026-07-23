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
        Schema::create('daily_statistics', function (Blueprint $table) {
            $table->id();
            $table->date('day')->unique();
            $table->unsignedInteger('login_num')->default(0)->index();
            $table->integer('active_dau')->default(0)->index();
            $table->unsignedInteger('register_num')->default(0)->index();
            $table->unsignedInteger('real_name_num')->default(0);
            $table->decimal('consume_amount', 14, 2)->default(0)->index();
            $table->decimal('recharge_amount', 14, 2)->default(0)->index();
            $table->unsignedInteger('recharge_user_count')->default(0);
            $table->decimal('withdraw_amount', 14, 2)->default(0)->index();
            $table->unsignedInteger('withdraw_user_count')->default(0);
            $table->decimal('new_recharge_amount', 14, 2)->default(0);
            $table->unsignedInteger('new_recharge_user_count')->default(0);
            $table->decimal('register_rate', 8, 4)->default(0);
            $table->decimal('real_name_rate', 8, 4)->default(0);
            $table->decimal('pay_rate', 8, 4)->default(0);
            $table->decimal('register_cost', 14, 2)->default(0);
            $table->decimal('pay_cost', 14, 2)->default(0);
            $table->decimal('arpu', 14, 2)->default(0);
            $table->decimal('arppu', 14, 2)->default(0);
            $table->decimal('new_customer_roa', 8, 4)->default(0);
            $table->decimal('roi', 8, 4)->default(0)->index();
            $table->unsignedInteger('d1_login_num')->default(0);
            $table->unsignedInteger('d3_login_num')->default(0);
            $table->unsignedInteger('d7_login_num')->default(0);
            $table->unsignedInteger('rd1_login_num')->default(0);
            $table->unsignedInteger('rd3_login_num')->default(0);
            $table->unsignedInteger('rd7_login_num')->default(0);
            $table->decimal('d1_rate', 8, 4)->default(0);
            $table->decimal('d3_rate', 8, 4)->default(0);
            $table->decimal('d7_rate', 8, 4)->default(0);
            $table->decimal('rd1_rate', 8, 4)->default(0);
            $table->decimal('rd3_rate', 8, 4)->default(0);
            $table->decimal('rd7_rate', 8, 4)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_statistics');
    }
};
