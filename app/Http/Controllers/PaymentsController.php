<?php

namespace App\Http\Controllers;

use App\Payments;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\CardTax;

class PaymentsController extends Controller
{

    public function updatePaymentDate(Request $request) {

        $payment = Payments::find($request->id);

        if( $request->data_pagamento_confirmado != null){
            $payment->data_pagamento_confirmado = Carbon::createFromFormat('d/m/Y', $request->data_pagamento_confirmado);
        }

        $payment->pago = 'SIM';
        $payment->alterado_por = $request->usuario;

        $payment->update();
        return response()->json(['result' => $payment]);
    }

    public function updatePayment(Request $request) {

        DB::table('pagamentos')->where('id', '=', $request->id)->delete();
        DB::table('pagamentos')->where('id_pagamento_original', '=', $request->id)->delete();

        $updatedPayment = new Payments();
        $remainingPayments = array();
        $paymentList = $this->recreatePayments($request);
        $isFirst = true;
        foreach ($paymentList as $payment){

            if($isFirst){
                $updatedPayment = $payment;
                $isFirst = false;
            }else
                $remainingPayments[] = $payment;
        }

        $updatedPayment->remainingPayments = $remainingPayments;
        return response()->json(['result' => $updatedPayment]);
    }

    public function removePayment(Request $request) {

        DB::table('pagamentos')->where('id', '=', $request->id)->delete();
        DB::table('pagamentos')->where('id_pagamento_original', '=', $request->id)->delete();

        return response()->json(['result' => 'OK']);
    }

    public function searchPaymentByDate(Request $request){

        $dataInicial = Carbon::create($request->ano, $request->mes, 1,0,0,0);
        $dataFinal = Carbon::create($request->ano, $request->mes, 1,0,0,0)->lastOfMonth();

        $payments = DB::table('pagamentos')
                        ->select(DB::raw('id, DATE_FORMAT(pagamentos.data_pagamento_efetuado,\'%d/%m/%Y\') as data_pagamento_efetuado, 
                                          DATE_FORMAT(pagamentos.data_pagamento_confirmado,\'%d/%m/%Y\') as data_pagamento_confirmado,
                                          DATE_FORMAT(pagamentos.data_prevista,\'%d/%m/%Y\') as data_prevista,
                                          nro_parcela, nro_parcelas, cliente, descricao,
                                          pagamentos.valor_parcela, pagamentos.pago, forma_pagamento'))
                        ->where([
                            ['data_prevista','>=', $dataInicial],
                            ['data_prevista','<=', $dataFinal],
                            ['forma_pagamento','<>','']])
                        ->orderBy('data_prevista', 'asc')
                        ->get();

        return response()->json(['result' => $payments]);

    }

    public function searchPaymentForecastByDate(Request $request){

        $payments = DB::table('pagamentos')
            ->select(DB::raw('id, DATE_FORMAT(data_pagamento_efetuado,\'%d/%m/%Y\') as data_pagamento_efetuado,
                              nro_parcelas, nro_parcela, cliente, descricao, forma_pagamento, valor_bruto, valor_parcela, valor_depois_taxa,
                              DATE_FORMAT(data_prevista,\'%d/%m/%Y\') as data_prevista, taxa_cartao_utilizada,
                              DATE_FORMAT(data_pagamento_confirmado,\'%d/%m/%Y\') as data_pagamento_confirmado'))
            ->whereRaw('id_pagamento_original is null and  data_pagamento_confirmado is null and '.
                'month(data_pagamento_efetuado) = ' . $request->mes . ' and year(data_pagamento_efetuado) = ' . $request->ano)
            ->get();


        foreach($payments as $payment){

            $remainingPayments = DB::table('pagamentos')
                ->select(DB::raw('id, DATE_FORMAT(data_pagamento_efetuado,\'%d/%m/%Y\') as data_pagamento_efetuado,
                              nro_parcelas, nro_parcela, cliente, descricao, forma_pagamento, valor_bruto, valor_parcela, valor_depois_taxa,
                              DATE_FORMAT(data_prevista,\'%d/%m/%Y\') as data_prevista, taxa_cartao_utilizada,
                              DATE_FORMAT(data_pagamento_confirmado,\'%d/%m/%Y\') as data_pagamento_confirmado'))
                ->whereRaw('id_pagamento_original = '. $payment->id .' and '.
                    'month(data_pagamento_efetuado) = ' . $request->mes . ' and year(data_pagamento_efetuado) = ' . $request->ano)
                ->get();

            $payment->remainingPayments = $remainingPayments;
        }

        return response()->json(['result' => $payments]);

    }

    public function createPayments(Request $request){

        $usedTax = 0;
        $paymentsList = array();

        $paymentForecastDate = Carbon::createFromFormat('d/m/Y', $request->data_pagamento_efetuado);
        $paymentDate = Carbon::createFromFormat('d/m/Y', $request->data_pagamento_efetuado);

        //Recover the correct tax for the treatment
        $pieces = explode(" ", $request->forma_pagamento);
        if ($request->forma_pagamento != "Dinheiro" && $request->forma_pagamento != "Cheque" && $request->forma_pagamento != null) {

            //Payment method has the format "<Carrier> <Payment Method>"
            $cardTaxes = CardTax::whereRaw('nro_parcelas_inicio <= ? and nro_parcelas_fim >= ? and 
                           bandeira = ? and forma_pagamento = ?',
                [$request->nro_parcelas, $request->nro_parcelas, $pieces[0], $pieces[1]])->get();

            foreach ($cardTaxes as $cardTax) {
                $usedTax = $cardTax->taxa;
            }

            if($pieces[1] == 'Débito'){

                $paymentForecastDate = $paymentForecastDate->addDays(5);

            }else{

                $paymentForecastDate = $paymentForecastDate->addDays(30);

            }
        }


        $percentage = bcsub('1',strval($usedTax),4);
        $finalPrice = bcmul($request->valor_bruto,$percentage,2);
        $paymentAmount = bcdiv($finalPrice, $request->nro_parcelas,2);

        $originalPayment = 0;
        for ($x = 1; $x <= $request->nro_parcelas; $x++){

            $payment = new Payments();
            $payment->nro_parcela = $x;
            $payment->data_prevista = $paymentForecastDate;
            $payment->data_pagamento_efetuado = $paymentDate;
            $payment->descricao = $request->descricao;
            $payment->valor_parcela = $paymentAmount;
            $payment->valor_bruto = $request->valor_bruto;
            $payment->valor_depois_taxa = $finalPrice;
            $payment->forma_pagamento = $request->forma_pagamento;
            $payment->nro_parcelas = $request->nro_parcelas;
            $payment->alterado_por = $request->usuario;
            $payment->cliente = $request->cliente;
            $payment->taxa_cartao_utilizada = $usedTax;

            if($request->forma_pagamento == 'Dinheiro'){
                $payment->data_pagamento_confirmado = $paymentDate;
            }

            if($x != 1){
                $payment->id_pagamento_original = $originalPayment;
            }

            $payment->save();

            if($x == 1){
                $originalPayment = $payment->id;
            }

            $paymentsList[] = $payment;
            $paymentForecastDate->addDays(30);
        }

        return response()->json(['result' => $paymentsList]);
    }

    private function recreatePayments(Request $request){

        $usedTax = 0;
        $paymentsList = array();

        $paymentForecastDate = Carbon::createFromFormat('d/m/Y', $request->data_pagamento_efetuado);
        $paymentDate = Carbon::createFromFormat('d/m/Y', $request->data_pagamento_efetuado);

        //Recover the correct tax for the treatment
        $pieces = explode(" ", $request->forma_pagamento);
        if ($request->forma_pagamento != "Dinheiro" && $request->forma_pagamento != "Cheque" && $request->forma_pagamento != null) {

            //Payment method has the format "<Carrier> <Payment Method>"
            $cardTaxes = CardTax::whereRaw('nro_parcelas_inicio <= ? and nro_parcelas_fim >= ? and 
                           bandeira = ? and forma_pagamento = ?',
                [$request->nro_parcelas, $request->nro_parcelas, $pieces[0], $pieces[1]])->get();

            foreach ($cardTaxes as $cardTax) {
                $usedTax = $cardTax->taxa;
            }

            if($pieces[1] == 'Débito'){

                $paymentForecastDate = $paymentForecastDate->addDays(5);

            }else{

                $paymentForecastDate = $paymentForecastDate->addDays(30);

            }
        }


        $percentage = bcsub('1',strval($usedTax),4);
        $finalPrice = bcmul($request->valor_bruto,$percentage,2);
        $paymentAmount = bcdiv($finalPrice, $request->nro_parcelas,2);

        $originalPayment = 0;
        for ($x = 1; $x <= $request->nro_parcelas; $x++){

            $payment = new Payments();
            $payment->nro_parcela = $x;
            $payment->data_prevista = $paymentForecastDate;
            $payment->data_pagamento_efetuado = $paymentDate;
            $payment->descricao = $request->descricao;
            $payment->valor_parcela = $paymentAmount;
            $payment->valor_bruto = $request->valor_bruto;
            $payment->valor_depois_taxa = $finalPrice;
            $payment->forma_pagamento = $request->forma_pagamento;
            $payment->nro_parcelas = $request->nro_parcelas;
            $payment->alterado_por = $request->usuario;
            $payment->cliente = $request->cliente;
            $payment->taxa_cartao_utilizada = $usedTax;

            if($request->forma_pagamento == 'Dinheiro'){
                $payment->data_pagamento_confirmado = $paymentDate;
            }

            if($x != 1){
                $payment->id_pagamento_original = $originalPayment;
            }

            $payment->save();

            if($x == 1){
                $originalPayment = $payment->id;
            }

            $paymentsList[] = $payment;
            $paymentForecastDate->addDays(30);
        }


        return $paymentsList;
    }

}
