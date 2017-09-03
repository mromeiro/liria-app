<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    protected $table = 'pagamentos';

    protected $dates = ['data_prevista','data_pagamento'];

    public $timestamps = false;

    public function getDataPrevistaAttribute($value){

        return Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('d/m/Y');
    }

    public function getDataPagamentoEfetuadoAttribute($value){

        if($value == null)
            return $value;

        if(is_string($value)){
            return Carbon::createFromFormat('Y-m-d', $value)->format('d/m/Y');
        }else{
            return $value->format('d/m/Y');
        }

    }

    public function getDataPagamentoConfirmadoAttribute($value){

        if($value == null)
            return $value;

        if(is_string($value)){
            return Carbon::createFromFormat('Y-m-d', $value)->format('d/m/Y');
        }else{
            return $value->format('d/m/Y');
        }

    }
}
