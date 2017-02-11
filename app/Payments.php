<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    protected $table = 'pagamentos';

    protected $dates = ['data_prevista','data_pagamento'];

    public $timestamps = false;
}
