<?php

namespace App\Http\Controllers;

use App\MontlyExpenses;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MonthlyExpensesController extends Controller
{

    public function getMonthlyExpenses(Request $request) {

        $expenses = MontlyExpenses::orderBy('dia_despesa','ASC')->get();

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

}
