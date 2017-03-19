<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ClientTreatments extends Model
{
    protected $table='tratamentos_cliente';

    protected $dates = ['data_inicio'];

    public function sessions()
    {
        return $this->hasMany('App\Sessions', 'tratamento_cliente_id')->orderBy('id','ASC');;
    }

    public function payments()
    {
        return $this->hasMany('App\Payments', 'tratamento_cliente_id')->orderBy('id','ASC');;
    }

    public function getDataInicioAttribute($value){

        if($value == null)
            return null;
        return Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('d/m/Y');
    }

    public function getCreatedAtAttribute($value){

        if($value == null)
            return null;
        return Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('d/m/Y H:i:s');
    }
}
