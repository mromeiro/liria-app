<?php

namespace App\Http\Controllers;

use App\Expenses;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExpensesController extends Controller
{

    public function create(Request $request) {

        $expenses = new Expenses();
        $expenseList = array();

        if($request->total_parcelas == null)
            $request->total_parcelas = 1;

        $paymentAmount = bcdiv($request->valor_total, $request->total_parcelas,2);

        $expenseDate = Carbon::createFromFormat('d/m/Y', $request->data_despesa);
        $paymentDate = Carbon::createFromFormat('d/m/Y', $request->data_despesa);
        if($request->fatura_fechada == 'true')
            $paymentDate->addDays(30);

        $originalPayment = 0;
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
            $expenses->valor_total = $request->valor_total;
            $expenses->previsao = $request->previsao;

            if($x != 1){
                $expenses->id_despesa_original = $originalPayment;
            }

            $expenses->save();

            if($x == 1){
                $originalPayment = $expenses->id;
            }

            $expenseList[] = $expenses;

            $expenses = new Expenses();
            $paymentDate->addDays(30);
        }
        return response()->json(['result' => $expenseList]);
    }

    public function reCreateExpenses(Request $request) {

        $expenses = new Expenses();
        $expenseList = array();

        if($request->total_parcelas == null)
            $request->total_parcelas = 1;

        $paymentAmount = bcdiv($request->valor_total, $request->total_parcelas,2);

        $expenseDate = Carbon::createFromFormat('d/m/Y', $request->data_despesa);
        $paymentDate = Carbon::createFromFormat('d/m/Y', $request->data_despesa);
        if($request->fatura_fechada == 'true')
            $paymentDate->addDays(30);

        $originalPayment = 0;
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
            $expenses->valor_total = $request->valor_total;
            $expenses->previsao = $request->previsao;

            if($x != 1){
                $expenses->id_despesa_original = $originalPayment;
            }

            $expenses->save();

            if($x == 1){
                $originalPayment = $expenses->id;
            }

            $expenseList[] = $expenses;

            $expenses = new Expenses();
            $paymentDate->addDays(30);
        }

        return $expenseList;
    }

    public function updateExpense(Request $request) {

        DB::table('despesas_diversas')->where('id', '=', $request->id)->delete();
        DB::table('despesas_diversas')->where('id_despesa_original', '=', $request->id)->delete();

        $updatedExpense = new Expenses();
        $remainingExpenses = array();
        $expenseList = $this->recreateExpenses($request);
        $isFirst = true;
        foreach ($expenseList as $expense){

            if($isFirst){
                $updatedExpense = $expense;
                $isFirst = false;
            }else
                $remainingExpenses[] = $expense;
        }

        $updatedExpense->remainingExpenses = $remainingExpenses;
        return response()->json(['result' => $updatedExpense]);
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

    public function searchExpenseForecastByDate(Request $request){

        $expenses = DB::table('despesas_diversas')
            ->select(DB::raw('id, DATE_FORMAT(data_despesa,\'%d/%m/%Y\') as data_despesa, previsao, 
                              descricao, metodo_pagamento, parcela, tipo, total_parcelas, valor_parcela, valor_total, 
                              DATE_FORMAT(data_parcela,\'%d/%m/%Y\') as data_parcela'))
            ->whereRaw('id_despesa_original is null and '.
                'month(data_despesa) = ' . $request->mes . ' and year(data_despesa) = ' . $request->ano)
            ->get();


        foreach($expenses as $expense){

            $remainingExpenses = DB::table('despesas_diversas')
                ->select(DB::raw('id, DATE_FORMAT(data_despesa,\'%d/%m/%Y\') as data_despesa,
                              descricao, metodo_pagamento, parcela, tipo, total_parcelas, valor_parcela, valor_total, 
                              DATE_FORMAT(data_parcela,\'%d/%m/%Y\') as data_parcela'))
                ->whereRaw('id_despesa_original = ' . $expense->id . ' and  '.
                    'month(data_despesa) = ' . $request->mes . ' and year(data_despesa) = ' . $request->ano)
                ->get();


            $expense->remainingExpenses = $remainingExpenses;
        }

        return response()->json(['result' => $expenses]);

    }
}
