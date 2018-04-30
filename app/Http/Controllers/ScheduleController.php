<?php

namespace App\Http\Controllers;

use App\Schedule;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    public function createEvent(Request $request){

        $schedule = new Schedule();
        $schedule->cliente = $request->cliente;
        $schedule->tratamento = $request->tratamento;
        $schedule->data_inicio = Carbon::createFromFormat('d/m/Y H:i', $request->dataIniEvento . ' ' . $request->horaIniEvento);
        $schedule->data_final = Carbon::createFromFormat('d/m/Y H:i', $request->dataFimEvento . ' ' . $request->horaFimEvento);
        $schedule->cor = $request->cor;
        $schedule->alterado_por = $request->usuario;

        $schedule->save();
        return response()->json(['result' => $schedule]);
    }

    public function updateEvent(Request $request){

        $schedule = Schedule::find($request->id);
        $schedule->cliente = $request->cliente;
        $schedule->tratamento = $request->tratamento;
        $schedule->data_inicio = Carbon::createFromFormat('d/m/Y H:i', $request->dataIniEvento . ' ' . $request->horaIniEvento);
        $schedule->data_final = Carbon::createFromFormat('d/m/Y H:i', $request->dataFimEvento . ' ' . $request->horaFimEvento);
        $schedule->cor = $request->cor;
        $schedule->alterado_por = $request->usuario;

        $schedule->update();

        return response()->json(['result' => $schedule]);
    }

    public function removeEvent(Request $request){

        Schedule::destroy($request->id);
        return response()->json(['result' => 'ok']);
    }

    public function getEvents(Request $request){

        $dataInicial = Carbon::createFromFormat('d/m/Y H:i', $request->dataIniEvento);
        $dataFinal = Carbon::createFromFormat('d/m/Y H:i', $request->dataFimEvento);

        $schedule = DB::table('agenda')
            ->select(DB::raw('agenda.cliente, agenda.tratamento, DATE_FORMAT(agenda.data_inicio,\'%d/%m/%Y %H:%i\') as data_inicio, 
                                          DATE_FORMAT(agenda.data_final,\'%d/%m/%Y %H:%i\') as data_final, agenda.id, agenda.cor'))
            ->where([
            ['agenda.data_inicio','>=', $dataInicial],
            ['agenda.data_final','<=', $dataFinal]])->get();

        return response()->json(['result' => $schedule]);
    }
}
