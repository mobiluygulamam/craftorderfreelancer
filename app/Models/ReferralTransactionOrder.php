<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralTransactionOrder extends Model
{
    use HasFactory;

    public static $status = [
        'Rejected',
        'In Progress',
        'Approved',
        'Cancelled',
    ];
    public function getCompany()
    {
        return $this->hasOne(User::class, 'id', 'req_user_id');
    }
}
