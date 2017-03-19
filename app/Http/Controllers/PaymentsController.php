<?php

namespace App\Http\Controllers;

use App\Payments;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PaymentsController extends Controller
{

    public function updatePaymentDate(Request $request) {

        $payment = Payments::find($request->id);

        if( $request->data_pagamento != null){
            $payment->data_pagamento = Carbon::createFromFormat('d/m/Y', $request->data_pagamento);
        }

        $payment->pago = 'SIM';
        $payment->alterado_por = $request->usuario;

        $payment->update();
        return response()->json(['result' => $payment]);
    }

    public function searchPaymentByDate(Request $request){

        $dataInicial = Carbon::create($request->ano, $request->mes, 1,0,0,0);
        $dataFinal = Carbon::create($request->ano, $request->mes, 1,0,0,0)->lastOfMonth();

        $payments = DB::table('pagamentos')
                        ->join('tratamentos_cliente', 'pagamentos.tratamento_cliente_id', '=', 'tratamentos_cliente.id')
                        ->join('clientes', 'tratamentos_cliente.cliente_id', '=', 'clientes.id')
                        ->select(DB::raw('pagamentos.id, DATE_FORMAT(pagamentos.data_pagamento,\'%d/%m/%Y\') as data_pagamento, 
                                          DATE_FORMAT(pagamentos.data_prevista,\'%d/%m/%Y\') as data_prevista,
                                          pagamentos.nro_parcela, tratamentos_cliente.nro_parcelas,
                                          clientes.name as nome_cliente, tratamentos_cliente.nome as nome_tratamento,
                                          pagamentos.valor_parcela, pagamentos.pago'))
                        ->where([
                            ['pagamentos.data_prevista','>=', $dataInicial],
                            ['pagamentos.data_prevista','<=', $dataFinal]])
                        ->orderBy('pagamentos.data_prevista', 'asc')
                        ->get();

        return response()->json(['result' => $payments]);

    }
}
