<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionType extends Model
{
    protected $fillable = [
        'name',
    ];

    public function post()
    {
        return $this->hasMany('App\Transaction', 'type');
    }
}
