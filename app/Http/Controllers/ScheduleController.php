<?php

namespace App\Http\Controllers;

use App\Schedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function createEvent(Request $request){

        $schedule = new Schedule();
        $schedule->cliente = $request->cliente;
        $schedule->tratamento = $request->tratamento;
        $schedule->data_inicio = Carbon::createFromFormat('d/m/Y H:i', $request->dataIniEvento . ' ' . $request->horaIniEvento);
        $schedule->data_final = Carbon::createFromFormat('d/m/Y H:i', $request->dataFimEvento . ' ' . $request->horaFimEvento);
        $schedule->alterado_por = $request->usuario;

        $schedule->save();
        return response()->json(['result' => $schedule]);
    }
}
