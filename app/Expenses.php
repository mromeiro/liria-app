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

        if(is_string($value)){
            return Carbon::createFromFormat('Y-m-d', $value)->format('d/m/Y');
        }else{
            return $value->format('d/m/Y');
        }
    }

    public function getDataParcelaAttribute($value){

        if($value == null)
            return $value;

        if(is_string($value)){
            return Carbon::createFromFormat('Y-m-d', $value)->format('d/m/Y');
        }else{
            return $value->format('d/m/Y');
        }

    }
}
