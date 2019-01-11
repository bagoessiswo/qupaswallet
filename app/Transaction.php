<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user', 'type', 'amount', 'message'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user');
    }

    public function type()
    {
        return $this->belongsTo('App\TransactionType', 'type');
    }
}
