<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $guarded = [];

    public function users() {
        return $this->belongsTo(User::class);
    }

    public function transaksi() {
        return $this->hasMany(Transaksi::class);
    }

    public function cartItems() {
        return $this->hasMany(CartItem::class);
    }
}
