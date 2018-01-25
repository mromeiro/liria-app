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
		                      valor_depois_taxa as valor_parcela, nro_parcela, nro_parcelas, DATE_FORMAT(data_prevista,\'%d/%m/%Y\') as data_prevista, 
		                      DATE_FORMAT(data_pagamento_efetuado,\'%d/%m/%Y\') as data_pagamento_efetuado, forma_pagamento'))
            ->whereRaw('month(data_pagamento_confirmado) = ' . $request->mes . ' and year(data_pagamento_confirmado) = ' . $request->ano .
                        ' and data_pagamento_confirmado is not null')
            ->orderByRaw('data_pagamento_efetuado', 'asc')
            ->get();

        $totalConfirmedPayments = DB::table('pagamentos')
            ->select(DB::raw('COALESCE(SUM(valor_depois_taxa),0) as total_confirmed'))
            ->whereRaw('month(data_pagamento_confirmado) = ' . $request->mes . ' and year(data_pagamento_confirmado) = ' . $request->ano .
                ' and data_pagamento_confirmado is not null')
            ->get();

        //Payments waiting confirmation does not have data_pagamento, only data_prevista
        $paymentsWaitingConfirmation = DB::table('pagamentos')
            ->select(DB::raw('cliente as nome_cliente, descricao, DATE_FORMAT(data_prevista,\'%d/%m/%Y\') as data_prevista, 
		                      valor_depois_taxa as valor_parcela, nro_parcela, nro_parcelas, DATE_FORMAT(data_pagamento_efetuado,\'%d/%m/%Y\') as data_pagamento_efetuado, forma_pagamento'))
            ->whereRaw('month(data_prevista) = ' . $request->mes . ' and year(data_prevista) = ' . $request->ano .
                ' and pagamentos.data_pagamento_confirmado is null and pagamentos.forma_pagamento <> \'Previsão\'')
            ->orderByRaw('data_prevista', 'asc')
            ->get();

        $totalPaymentsWaitingConfirmation = DB::table('pagamentos')
            ->select(DB::raw('COALESCE(SUM(valor_depois_taxa),0) as total_waiting_confirmation'))
            ->whereRaw('month(data_prevista) = ' . $request->mes . ' and year(data_prevista) = ' . $request->ano .
                ' and data_pagamento_confirmado is null and pagamentos.forma_pagamento <> \'Previsão\'')
            ->get();

        //Payments waiting confirmation does not have data_pagamento, only data_prevista
        $forcastPayments = DB::table('pagamentos')
            ->select(DB::raw('cliente as nome_cliente, descricao, DATE_FORMAT(data_prevista,\'%d/%m/%Y\') as data_prevista, 
		                      valor_depois_taxa as valor_parcela, nro_parcela, nro_parcelas, DATE_FORMAT(data_pagamento_efetuado,\'%d/%m/%Y\') as data_pagamento_efetuado, forma_pagamento'))
            ->whereRaw('month(data_prevista) = ' . $request->mes . ' and year(data_prevista) = ' . $request->ano .
                ' and pagamentos.data_pagamento_confirmado is null and pagamentos.forma_pagamento = \'Previsão\'')
            ->orderByRaw('data_prevista', 'asc')
            ->get();

        $totalPaymentsForcast = DB::table('pagamentos')
            ->select(DB::raw('COALESCE(SUM(valor_depois_taxa),0) as total_forcast'))
            ->whereRaw('month(data_prevista) = ' . $request->mes . ' and year(data_prevista) = ' . $request->ano .
                ' and data_pagamento_confirmado is null and pagamentos.forma_pagamento = \'Previsão\'')
            ->get();

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
        $report->totalForcastPayments = $totalPaymentsForcast[0]->total_forcast;
        $report->totalExpenses = $totalExpenses;
        $report->totalExpensesWaitingToConfirm = $totalExpensesWaitingToConfirm;

        $report->confirmedPayments = $confirmedPayments;
        $report->paymentsToConfirm = $paymentsWaitingConfirmation;
        $report->forcastPayments= $forcastPayments;
        $report->expenses = $expenses;
        $report->expensesWaitingToConfirm = $expensesWaitingToConfirm;

        return response()->json(['result' => $report]);
    }
}
