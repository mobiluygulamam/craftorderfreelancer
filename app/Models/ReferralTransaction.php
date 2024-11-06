<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralTransaction extends Model
{
    use HasFactory;

    public function getUserDetails()
    {
        return $this->hasOne(User::class, 'id', 'company_id');
    }

    public function getPlan()
    {
        return $this->hasOne(Plan::class, 'id', 'plan_id');
    }
}
