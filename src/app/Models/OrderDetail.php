<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'first_name',
        'last_name',
        'first_kana',
        'last_kana',
        'phone',
        'billing_address1',
        'billing_address2',
        'billing_city',
        'billing_state',
        'billing_zipcode',
        'billing_country_code',
        'shipping_address1',
        'shipping_address2',
        'shipping_city',
        'shipping_state',
        'shipping_zipcode',
        'shipping_country_code'
    ];

    public function billingCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'billing_country_code', 'code');
    }

    public function shippingCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'shipping_country_code', 'code');
    }
}
