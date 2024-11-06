<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'monthly_price',
        'annual_price',
        'storage_limit',
        'status',
        'enable_chatgpt',
        'trial_days',
        'max_workspaces',
        'max_users',
        'max_clients',
        'max_projects',
        'description',
        'image',
        'is_trial_disable',
    ];

    public function arrDuration()
    {
        return [
            'Unlimited' => 'Unlimited',
            'Month' => 'Per Month',
            'Year' => 'Per Year',
        ];
    }



}
