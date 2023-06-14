<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name' ,
        'start_date',
        'end_date',
        'price',
        'reminder',
        'company_name',
        'company_number',
        'agent_name',
        'agent_number',
        'attachment',
        'status',
        'category_id'
    ];
}
