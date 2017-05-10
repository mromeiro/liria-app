<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Expenses extends Model
{
    protected $table = 'despesas_diversas';

    public $timestamps = false;

    public function getDataDespesaAttribute($value){

        if($value == null)
            return $value;

        return Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('d/m/Y');
    }

    public function getDataParcelaAttribute($value){

        if($value == null)
            return $value;

        return Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('d/m/Y');
    }
}
