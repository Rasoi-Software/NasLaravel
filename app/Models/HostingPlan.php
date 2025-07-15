<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HostingPlan extends Model
{
    protected $fillable = [
        'stripe_product_id',
        'stripe_price_id',
        'name',
        'description',
        'amount',
        'currency',
        'interval',
    ];
}
