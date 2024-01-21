<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'remark',
        'username',
        'order_count',
        'workingdate',
        'companies',
        'lotterycode',
        'betcount',
        'totalamount',
    ];
}
