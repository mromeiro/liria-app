<?php

namespace App\Http\Controllers;

use App\MontlyExpenses;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MonthlyExpensesController extends Controller
{

    public function getMonthlyExpenses(Request $request) {

        $expenses = MontlyExpenses::all();

        return response()->json(['result' => $expenses]);
    }

    public function create(Request $request) {

        $expenses = new MontlyExpenses();
        $expenses->descricao = $request->descricao;
        $expenses->dia_despesa = $request->dia_despesa;
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
        $file->move('images/receipts', $fileName);

        $expense = MontlyExpenses::find($request->expenseId);
        $expense->recibo = $fileName;
        $expense->update();

        return response()->json(['result' => $expense]);
    }
}
