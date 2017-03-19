<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Clients extends Model
{
   protected $table='clientes';

   protected $dates = ['data_nascimento'];

    public function treatments()
    {
        return $this->hasMany('App\ClientTreatments', 'cliente_id')->orderBy('id','DESC');
    }

    public function getDataNascimentoAttribute($value){

        return Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('d/m/Y');
    }
}
