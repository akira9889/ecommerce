<?php

namespace App\Models;

use App\Enums\AddressType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Profile extends Model
{
    use HasFactory;

    protected $fillable =  ['user_id', 'first_name', 'last_name', 'first_kana', 'last_kana', 'phone', 'type'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    private function _getAddresses(): HasOne
    {
        return $this->hasOne(ProfileAddress::class);
    }

    public function shippingAddress(): HasOne
    {
        return $this->_getAddresses()->where('type', '=', AddressType::Shipping->value);
    }

    public function billingAddress(): HasOne
    {
        return $this->_getAddresses()->where('type', '=', AddressType::Billing->value);
    }
}
