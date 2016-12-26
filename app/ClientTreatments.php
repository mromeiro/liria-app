<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientTreatments extends Model
{
    protected $table='tratamentos_cliente';

    public function sessions()
    {
        return $this->hasMany('App\Sessions', 'tratamento_cliente_id');
    }

    public function payments()
    {
        return $this->hasMany('App\Payments', 'tratamento_cliente_id');
    }
}
