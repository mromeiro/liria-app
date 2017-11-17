<?php

namespace App\Http\Controllers;

use App\Report;
use App\Expenses;
use App\Utils\Constants;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class ReportController extends Controller
{
    public function scheduleReport(Request $request){

    }

    public function conciliationReport(Request $request){

        $configList = ConfigController::getConfigForController();
        $sumupToken = SumupController::getSumupToken($configList);

        //Get the transaction history for conciliation
        $client = new Client();
        $result = $client->get($configList[Constants::$SUMUP_TRANSACTION_HISTORY_API_CONFIG], [
            'headers' => [
                'Authorization' => 'Bearer ' . $sumupToken,
            ]
        ]);

        $SumupData = json_decode($result->getBody());

    }

    public function monthlyBalanceReport(Request $request) {

        $confirmedPayments = DB::table('pagamentos')
            ->select(DB::raw('cliente as nome_cliente, descricao, DATE_FORMAT(data_pagamento_confirmado,\'%d/%m/%Y\') as data_pagamento_confirmado, 
		                      valor_parcela, nro_parcela, nro_parcelas, DATE_FORMAT(data_prevista,\'%d/%m/%Y\') as data_prevista, 
		                      DATE_FORMAT(data_pagamento_efetuado,\'%d/%m/%Y\') as data_pagamento_efetuado'))
            ->whereRaw('month(data_pagamento_confirmado) = ' . $request->mes . ' and year(data_pagamento_confirmado) = ' . $request->ano .
                        ' and data_pagamento_confirmado is not null')
            ->get();

        $totalConfirmedPayments = DB::table('pagamentos')
            ->select(DB::raw('COALESCE(SUM(valor_parcela),0) as total_confirmed'))
            ->whereRaw('month(data_pagamento_confirmado) = ' . $request->mes . ' and year(data_pagamento_confirmado) = ' . $request->ano .
                ' and data_pagamento_confirmado is not null')
            ->get();

        //Payments waiting confirmation does not have data_pagamento, only data_prevista
        $paymentsWaitingConfirmation = DB::table('pagamentos')
            ->select(DB::raw('cliente as nome_cliente, descricao, DATE_FORMAT(data_prevista,\'%d/%m/%Y\') as data_prevista, 
		                      valor_parcela, nro_parcela, nro_parcelas, DATE_FORMAT(data_pagamento_efetuado,\'%d/%m/%Y\') as data_pagamento_efetuado'))
            ->whereRaw('month(data_prevista) = ' . $request->mes . ' and year(data_prevista) = ' . $request->ano .
                ' and pagamentos.data_pagamento_confirmado is null')
            ->get();

        $totalPaymentsWaitingConfirmation = DB::table('pagamentos')
            ->select(DB::raw('COALESCE(SUM(valor_parcela),0) as total_waiting_confirmation'))
            ->whereRaw('month(data_prevista) = ' . $request->mes . ' and year(data_prevista) = ' . $request->ano .
                ' and data_pagamento_confirmado is null')
            ->get();

        /*$treatmentsWaitingConfirmation = DB::table('pagamentos')
            ->join('tratamentos_cliente', 'pagamentos.tratamento_cliente_id', '=', 'tratamentos_cliente.id')
            ->join('clientes', 'tratamentos_cliente.cliente_id', '=', 'clientes.id')
            ->select(DB::raw('clientes.name as nome_cliente, tratamentos_cliente.nome as tratamento, 
		                      pagamentos.valor_parcela, pagamentos.nro_parcela, tratamentos_cliente.nro_parcelas, DATE_FORMAT(pagamentos.data_prevista,\'%d/%m/%Y\') as data_prevista'))
            ->whereRaw('month(pagamentos.data_prevista) = ' . $request->mes . ' and year(pagamentos.data_prevista) = ' . $request->ano .
                ' and pagamentos.data_pagamento is null' .
                ' and tratamentos_cliente.forma_pagamento is null')
            ->get();

        $totalTreatmentsWaitingConfirmation = DB::table('pagamentos')
            ->join('tratamentos_cliente', 'pagamentos.tratamento_cliente_id', '=', 'tratamentos_cliente.id')
            ->join('clientes', 'tratamentos_cliente.cliente_id', '=', 'clientes.id')
            ->select(DB::raw('COALESCE(SUM(valor_parcela),0) as total_treatment_waiting_confirmation'))
            ->whereRaw('month(pagamentos.data_prevista) = ' . $request->mes . ' and year(pagamentos.data_prevista) = ' . $request->ano .
                ' and pagamentos.data_pagamento is null' .
                ' and tratamentos_cliente.forma_pagamento is null')
            ->get();*/

        //Expenses
        $initialDate = Carbon::create($request->ano, $request->mes, 1,0,0,0);
        $finalDate = Carbon::create($request->ano, $request->mes, 1,0,0,0)->lastOfMonth();

        $expenses = Expenses::where('data_parcela', '>=', $initialDate)
            ->where('data_parcela', '<=', $finalDate)
            ->where('previsao','=','false')
            ->orderBy('tipo', 'asc')->orderBy('data_parcela', 'asc')->get();

        $totalExpenses = Expenses::where('data_parcela', '>=', $initialDate)
            ->where('data_parcela', '<=', $finalDate)
            ->where('previsao','=','false')
            ->orderBy('tipo', 'asc')->orderBy('data_parcela', 'asc')->sum('valor_parcela');

        $expensesWaitingToConfirm = Expenses::where('data_parcela', '>=', $initialDate)
            ->where('data_parcela', '<=', $finalDate)
            ->where('previsao','=','true')
            ->orderBy('tipo', 'asc')->orderBy('data_parcela', 'asc')->get();

        $totalExpensesWaitingToConfirm = Expenses::where('data_parcela', '>=', $initialDate)
            ->where('data_parcela', '<=', $finalDate)
            ->where('previsao','=','true')
            ->orderBy('tipo', 'asc')->orderBy('data_parcela', 'asc')->sum('valor_parcela');

        $report = new Report();
        $report->totalConfirmedPayments = $totalConfirmedPayments[0]->total_confirmed;
        $report->totalPaymentsToConfirm = $totalPaymentsWaitingConfirmation[0]->total_waiting_confirmation;
        //$report->totalTreatmentsNotConfirmed = $totalTreatmentsWaitingConfirmation[0]->total_treatment_waiting_confirmation;
        $report->totalExpenses = $totalExpenses;
        $report->totalExpensesWaitingToConfirm = $totalExpensesWaitingToConfirm;
        $report->confirmedPayments = $confirmedPayments;
        $report->paymentsToConfirm = $paymentsWaitingConfirmation;
        //$report->treatmentsNotConfirmed = $treatmentsWaitingConfirmation;
        $report->expenses = $expenses;
        $report->expensesWaitingToConfirm = $expensesWaitingToConfirm;

        return response()->json(['result' => $report]);
    }
}
