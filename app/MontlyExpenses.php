<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MontlyExpenses extends Model
{
    protected $table = 'despesas_mensais';

    public $timestamps = false;
}
