<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_id',
        'name',
        'email',
        'card_number',
        'card_exp_month',
        'card_exp_year',
        'plan_name',
        'plan_id',
        'price',
        'price_currency',
        'payment_frequency',
        'txn_id',
        'payment_status',
        'receipt',
        'payment_type',
        'user_id'
    ];

    public static function total_orders()
    {
        return Order::count();
    }

    public static function total_orders_price()
    {
        return Order::sum('price');
    }

    public function total_coupon_used()
    {
        return $this->hasOne('App\Models\UserCoupon', 'order', 'order_id');
    }

    // Get the latest order for refund
    public static function isLatestOrder($user_id)
    {
        $Order = Order::where('user_id', $user_id)->latest()->get();
        return $Order[0]->id;
    }
}
