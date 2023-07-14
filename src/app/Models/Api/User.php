<?php

namespace App\Models\Api;

use App\Enums\ProfileType;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends \App\Models\User
{
    public function adminProfile(): HasOne
    {
        return $this->hasOne(Profile::class)->where('type', ProfileType::Admin->value);
    }
}
