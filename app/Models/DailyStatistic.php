<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyStatistic extends Model
{
    protected $fillable = [
        'day',
        'login_num',
        'active_dau',
        'register_num',
        'real_name_num',
        'consume_amount',
        'recharge_amount',
        'recharge_user_count',
        'withdraw_amount',
        'withdraw_user_count',
        'new_recharge_amount',
        'new_recharge_user_count',
        'register_rate',
        'real_name_rate',
        'pay_rate',
        'register_cost',
        'pay_cost',
        'arpu',
        'arppu',
        'new_customer_roa',
        'roi',
        'd1_login_num',
        'd3_login_num',
        'd7_login_num',
        'rd1_login_num',
        'rd3_login_num',
        'rd7_login_num',
        'd1_rate',
        'd3_rate',
        'd7_rate',
        'rd1_rate',
        'rd3_rate',
        'rd7_rate',
    ];

    public static function booted(): void
    {
        static::saving(static function (self $model): void {
            $model->recalculate();
        });
    }

    public function recalculate(): void
    {
        $this->active_dau = (int) $this->login_num - (int) $this->register_num;
        $this->register_rate = $this->ratio($this->register_num, $this->login_num);
        $this->real_name_rate = $this->ratio($this->real_name_num, $this->register_num);
        $this->pay_rate = $this->ratio($this->new_recharge_user_count, $this->register_num);
        $this->register_cost = $this->moneyRatio($this->consume_amount, $this->register_num);
        $this->pay_cost = $this->moneyRatio($this->consume_amount, $this->recharge_user_count);
        $this->arpu = $this->moneyRatio($this->recharge_amount, $this->login_num);
        $this->arppu = $this->moneyRatio($this->recharge_amount, $this->recharge_user_count);
        $this->new_customer_roa = $this->ratio($this->new_recharge_amount, $this->consume_amount);
        $this->roi = $this->ratio(((float) $this->recharge_amount) - ((float) $this->withdraw_amount), $this->consume_amount);
        $this->d1_rate = $this->ratio($this->d1_login_num, $this->register_num);
        $this->d3_rate = $this->ratio($this->d3_login_num, $this->register_num);
        $this->d7_rate = $this->ratio($this->d7_login_num, $this->register_num);
        $this->rd1_rate = $this->ratio($this->rd1_login_num, $this->new_recharge_user_count);
        $this->rd3_rate = $this->ratio($this->rd3_login_num, $this->new_recharge_user_count);
        $this->rd7_rate = $this->ratio($this->rd7_login_num, $this->new_recharge_user_count);
    }

    private function ratio(mixed $numerator, mixed $denominator): float
    {
        $denominator = (float) $denominator;
        if ($denominator == 0.0) {
            return 0.0;
        }

        return round((float) $numerator / $denominator, 4);
    }

    private function moneyRatio(mixed $numerator, mixed $denominator): float
    {
        $denominator = (float) $denominator;
        if ($denominator == 0.0) {
            return 0.0;
        }

        return round((float) $numerator / $denominator, 2);
    }

    protected function casts(): array
    {
        return [
            'day' => 'date',
            'consume_amount' => 'decimal:2',
            'recharge_amount' => 'decimal:2',
            'withdraw_amount' => 'decimal:2',
            'new_recharge_amount' => 'decimal:2',
            'register_rate' => 'decimal:4',
            'real_name_rate' => 'decimal:4',
            'pay_rate' => 'decimal:4',
            'register_cost' => 'decimal:2',
            'pay_cost' => 'decimal:2',
            'arpu' => 'decimal:2',
            'arppu' => 'decimal:2',
            'new_customer_roa' => 'decimal:4',
            'roi' => 'decimal:4',
            'd1_rate' => 'decimal:4',
            'd3_rate' => 'decimal:4',
            'd7_rate' => 'decimal:4',
            'rd1_rate' => 'decimal:4',
            'rd3_rate' => 'decimal:4',
            'rd7_rate' => 'decimal:4',
        ];
    }
}
