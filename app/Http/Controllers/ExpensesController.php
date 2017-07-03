<?php

namespace App\Http\Controllers;

use App\Expenses;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExpensesController extends Controller
{

    public function create(Request $request) {

        $expenses = new Expenses();
        $expenseList = array();

        if($request->total_parcelas == null)
            $request->total_parcelas = 1;

        $paymentAmount = bcdiv($request->valor, $request->total_parcelas,2);

        $expenseDate = Carbon::createFromFormat('d/m/Y', $request->data_despesa);
        $paymentDate = Carbon::createFromFormat('d/m/Y', $request->data_despesa);
        if($request->fatura_fechada == 'true')
            $paymentDate->addDays(30);

        for($x = 1 ; $x <= $request->total_parcelas ; $x++){

            $expenses->alterado_por = $request->usuario;
            $expenses->tipo = $request->tipo;
            $expenses->descricao = $request->descricao;
            $expenses->data_parcela = new Carbon($paymentDate);
            $expenses->data_despesa = $expenseDate;
            $expenses->metodo_pagamento = $request->metodo_pagamento;
            $expenses->alterado_por = $request->usuario;
            $expenses->valor_parcela = $paymentAmount;
            $expenses->total_parcelas = $request->total_parcelas;
            $expenses->parcela = $x;
            $expenses->recibo = $request->recibo;
            $expenses->valor_total = $request->valor;

            $expenses->save();
            $expenseList[] = $expenses;

            $expenses = new Expenses();
            $paymentDate->addDays(30);
        }
        return response()->json(['result' => $expenseList]);
    }

    public function saveReceipt(Request $request) {

        $file = $request->file('file');

        //filename
        $today = Carbon::now()->format('dmyhis');
        $fileNamePieces = explode(".", $file[0]->getClientOriginalName());
        $fileName = $fileNamePieces[0] . '_' . $today . '.' . $fileNamePieces[1];

        //Save file
        $file[0]->move('receipts', $fileName);

        return response()->json(['result' => $fileName]);
    }

    public function getExpenses(Request $request){

        $initialDate = Carbon::create($request->ano, $request->mes, 1,0,0,0);
        $finalDate = Carbon::create($request->ano, $request->mes, 1,0,0,0)->lastOfMonth();

        $expenses = Expenses::where('data_parcela', '>=', $initialDate)
                    ->where('data_parcela', '<=', $finalDate)
                    ->orderBy('tipo', 'asc')->orderBy('data_parcela', 'asc')->get();

        return response()->json(['result' => $expenses]);
    }
}
