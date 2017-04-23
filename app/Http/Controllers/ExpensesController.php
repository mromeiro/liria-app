<?php

namespace App\Http\Controllers;

use App\Expenses;
use Illuminate\Http\Request;

class ExpensesController extends Controller
{

    public function create(Request $request) {

        $expenses = new MontlyExpenses();
        $expenses->tipo = $request->tipo;
        $expenses->descricao = $request->descricao;
        $expenses->data_despesa = $request->data_despesa;
        $expenses->metodo_pagamento = $request->metodo_pagamento;
        $expenses->alterado_por = $request->usuario;

        $expenses->save();
        return response()->json(['result' => $expenses]);
    }

    public function saveReceipt(Request $request) {

        $file = $request->file('file');

        //filename
        $today = Carbon::now()->format('dmyhis');
        $fileNamePieces = explode(".", $file->getClientOriginalName());
        $fileName = $fileNamePieces[0] . '_' . $today . '.' . $fileNamePieces[1];

        //Save file
        $file->move('receipts', $fileName);

        $expense = Expenses::find($request->expenseId);
        $expense->recibo = $fileName;
        $expense->update();

        return response()->json(['result' => $expense]);
    }
}
