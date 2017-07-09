<?php

namespace App\Http\Controllers;

use App\Report;
use App\Expenses;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function monthlyBalanceReport(Request $request) {

        $confirmedPayments = DB::table('pagamentos')
            ->join('tratamentos_cliente', 'pagamentos.tratamento_cliente_id', '=', 'tratamentos_cliente.id')
            ->join('clientes', 'tratamentos_cliente.cliente_id', '=', 'clientes.id')
            ->select(DB::raw('clientes.name as nome_cliente, tratamentos_cliente.nome as tratamento, DATE_FORMAT(pagamentos.data_pagamento,\'%d/%m/%Y\') as data_pagamento, 
		                      pagamentos.valor_parcela, pagamentos.nro_parcela, tratamentos_cliente.nro_parcelas, DATE_FORMAT(pagamentos.data_prevista,\'%d/%m/%Y\') as data_prevista'))
            ->whereRaw('month(pagamentos.data_pagamento) = ' . $request->mes . ' and year(pagamentos.data_pagamento) = ' . $request->ano .
                        ' and tratamentos_cliente.forma_pagamento is not null')
            ->get();

        $totalConfirmedPayments = DB::table('pagamentos')
            ->join('tratamentos_cliente', 'pagamentos.tratamento_cliente_id', '=', 'tratamentos_cliente.id')
            ->join('clientes', 'tratamentos_cliente.cliente_id', '=', 'clientes.id')
            ->select(DB::raw('COALESCE(SUM(valor_parcela),0) as total_confirmed'))
            ->whereRaw('month(pagamentos.data_pagamento) = ' . $request->mes . ' and year(pagamentos.data_pagamento) = ' . $request->ano .
                ' and tratamentos_cliente.forma_pagamento is not null')
            ->get();

        //Payments waiting confirmation does not have data_pagamento, only data_prevista
        $paymentsWaitingConfirmation = DB::table('pagamentos')
            ->join('tratamentos_cliente', 'pagamentos.tratamento_cliente_id', '=', 'tratamentos_cliente.id')
            ->join('clientes', 'tratamentos_cliente.cliente_id', '=', 'clientes.id')
            ->select(DB::raw('clientes.name as nome_cliente, tratamentos_cliente.nome as tratamento, 
		                      pagamentos.valor_parcela, pagamentos.nro_parcela, tratamentos_cliente.nro_parcelas, DATE_FORMAT(pagamentos.data_prevista,\'%d/%m/%Y\') as data_prevista'))
            ->whereRaw('month(pagamentos.data_prevista) = ' . $request->mes . ' and year(pagamentos.data_prevista) = ' . $request->ano .
                ' and pagamentos.data_pagamento is null' .
                ' and tratamentos_cliente.forma_pagamento is not null')
            ->get();

        $totalPaymentsWaitingConfirmation = DB::table('pagamentos')
            ->join('tratamentos_cliente', 'pagamentos.tratamento_cliente_id', '=', 'tratamentos_cliente.id')
            ->join('clientes', 'tratamentos_cliente.cliente_id', '=', 'clientes.id')
            ->select(DB::raw('COALESCE(SUM(valor_parcela),0) as total_waiting_confirmation'))
            ->whereRaw('month(pagamentos.data_prevista) = ' . $request->mes . ' and year(pagamentos.data_prevista) = ' . $request->ano .
                ' and pagamentos.data_pagamento is null' .
                ' and tratamentos_cliente.forma_pagamento is not null')
            ->get();

        $treatmentsWaitingConfirmation = DB::table('pagamentos')
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
            ->get();

        //Expenses
        $initialDate = Carbon::create($request->ano, $request->mes, 1,0,0,0);
        $finalDate = Carbon::create($request->ano, $request->mes, 1,0,0,0)->lastOfMonth();

        $expenses = Expenses::where('data_parcela', '>=', $initialDate)
            ->where('data_parcela', '<=', $finalDate)
            ->orderBy('tipo', 'asc')->orderBy('data_parcela', 'asc')->get();

        $totalExpenses = Expenses::where('data_parcela', '>=', $initialDate)
            ->where('data_parcela', '<=', $finalDate)
            ->orderBy('tipo', 'asc')->orderBy('data_parcela', 'asc')->sum('valor_parcela');

        $report = new Report();
        $report->totalConfirmedPayments = $totalConfirmedPayments[0]->total_confirmed;
        $report->totalPaymentsToConfirm = $totalPaymentsWaitingConfirmation[0]->total_waiting_confirmation;
        $report->totalTreatmentsNotConfirmed = $totalTreatmentsWaitingConfirmation[0]->total_treatment_waiting_confirmation;
        $report->totalExpenses = $totalExpenses;
        $report->confirmedPayments = $confirmedPayments;
        $report->paymentsToConfirm = $paymentsWaitingConfirmation;
        $report->treatmentsNotConfirmed = $treatmentsWaitingConfirmation;
        $report->expenses = $expenses;

        return response()->json(['result' => $report]);
    }
}