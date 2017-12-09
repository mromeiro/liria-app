<?php

namespace App\Http\Controllers;

use App\Payments;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\CardTax;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

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

        $paymentList = $this->createPaymentsInternal($request);

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

    public function createPaymentsSumup(Request $request){

        $paymentsList = array();
        $configList = ConfigController::getConfigForController();

        $transaction = SumupController::getTransactionDetails($configList, $request->id_transacao);
        //$sumup = "{\"id\": \"7dee114a-366d-4eb0-a7f2-4cbe1547b6c6\", \"transaction_code\": \"TV3SQ3SS9L\", \"merchant_code\": \"MFN22FKU\", \"username\": \"nathaly_biomedicina@hotmail.com\", \"amount\": 245, \"vat_amount\": 0, \"tip_amount\": 0, \"currency\": \"BRL\", \"timestamp\": \"2017-10-04T22:12:12.639Z\", \"lat\": -23.5311582857529, \"lon\": -47.4668258077557, \"horizontal_accuracy\": 65, \"status\": \"SUCCESSFUL\", \"payment_type\": \"POS\", \"simple_payment_type\": \"EMV\", \"entry_mode\": \"chip\", \"verification_method\": \"offline PIN\", \"card\": {\"last_4_digits\": \"3715\", \"type\": \"ELO\"}, \"local_time\": \"2017-10-04T22:12:12.639Z\", \"payout_date\": \"2017-10-31\", \"payout_plan\": \"TRUE_INSTALLMENT\", \"payout_type\": \"BANK_ACCOUNT\", \"installments_count\": 2, \"process_as\": \"CREDIT\", \"products\": [{\"name\": \"\", \"price\": 245, \"quantity\": 1, \"total_price\": 245 } ], \"vat_rates\": [], \"transaction_events\": [{\"id\": 59042731, \"event_type\": \"PAYOUT\", \"status\": \"PAID_OUT\", \"amount\": 117.72, \"due_date\": \"2017-12-03\", \"date\": \"2017-11-28\", \"installment_number\": 2, \"timestamp\": \"2017-10-04T22:12:40.360Z\"}, {\"id\": 59042730, \"event_type\": \"PAYOUT\", \"status\": \"PAID_OUT\", \"amount\": 117.72, \"due_date\": \"2017-11-03\", \"date\": \"2017-10-31\", \"installment_number\": 1, \"timestamp\": \"2017-10-04T22:12:40.356Z\"} ], \"simple_status\": \"SUCCESSFUL\", \"links\": [{\"rel\": \"receipt\", \"href\": \"https://receipts-ng.sumup.com/v0.1/receipts/7dee114a-366d-4eb0-a7f2-4cbe1547b6c6?mid=MFN22FKU&format=svg\", \"type\": \"image/svg+xml\"}, {\"rel\": \"receipt\", \"href\": \"https://receipts-ng.sumup.com/v0.1/receipts/7dee114a-366d-4eb0-a7f2-4cbe1547b6c6?mid=MFN22FKU&format=png\", \"type\": \"image/png\"}, {\"rel\": \"refund\", \"href\": \"https://api.sumup.com/v0.1/me/refund/7dee114a-366d-4eb0-a7f2-4cbe1547b6c6\", \"type\": \"application/json\", \"min_amount\": 245, \"max_amount\": 245, \"disclaimer\": \"settled_deduction\"} ], \"events\": [{\"id\": 59042731, \"transaction_id\": \"7dee114a-366d-4eb0-a7f2-4cbe1547b6c6\", \"type\": \"PAYOUT\", \"status\": \"PAID_OUT\", \"amount\": 117.72, \"timestamp\": \"2017-11-28T12:00:00.000Z\", \"fee_amount\": 4.78, \"installment_number\": 2, \"payout_type\": \"BANK_ACCOUNT\", \"payout_id\": 10293, \"deducted_amount\": 0, \"deducted_fee_amount\": 0 }, {\"id\": 59042730, \"transaction_id\": \"7dee114a-366d-4eb0-a7f2-4cbe1547b6c6\", \"type\": \"PAYOUT\", \"status\": \"PAID_OUT\", \"amount\": 117.72, \"timestamp\": \"2017-10-31T12:00:00.000Z\", \"fee_amount\": 4.78, \"installment_number\": 1, \"payout_type\": \"BANK_ACCOUNT\", \"payout_id\": 9971, \"deducted_amount\": 0, \"deducted_fee_amount\": 0 } ], \"payouts_received\": 2, \"payouts_total\": 2, \"location\": {\"lat\": -23.5311582857529, \"lon\": -47.4668258077557, \"horizontal_accuracy\": 65 }, \"tax_enabled\": true, \"auth_code\": \"624134\", \"internal_id\": 59379929 }";
        //$transaction = json_decode($sumup);//

        Log::info('Sumup data: '. json_encode($transaction));

        $originalPayment = 0;
        for ($x = 0; $x < $transaction->installments_count; $x++){

            $payment = new Payments();
            $payment->id_transacao = $transaction->transaction_code;
            $payment->nro_parcela = $transaction->events[$x]->installment_number;
            $payment->data_prevista = Carbon::createFromFormat('Y-m-d', $transaction->transaction_events[$x]->due_date);
            $payment->data_pagamento_efetuado = Carbon::createFromFormat('Y-m-d', substr($transaction->timestamp,0,10));
            $payment->descricao = $request->descricao;
            $payment->valor_parcela = bcadd($transaction->events[$x]->amount, $transaction->events[$x]->fee_amount,2);
            $payment->valor_bruto = $transaction->amount;
            $payment->valor_depois_taxa = $transaction->events[$x]->amount;
            $payment->forma_pagamento = $request->forma_pagamento;
            $payment->nro_parcelas = $transaction->installments_count;
            $payment->alterado_por = $request->usuario;
            $payment->cliente = $request->cliente;

            if($x != 0){
                $payment->id_pagamento_original = $originalPayment;
            }

            $payment->save();

            if($x == 0){
                $originalPayment = $payment->id;
            }

            $paymentsList[] = $payment;
        }

        return $paymentsList;
    }

    public function createPayments(Request $request){

        if($request->forma_pagamento == 'Cartão'){

            $paymentsList = $this->createPaymentsSumup($request);

        }else {

            $paymentsList = $this->createPaymentsCash($request);
        }

        return response()->json(['result' => $paymentsList]);
    }

    private function createPaymentsCash(Request $request){

        $paymentsList = array();

        $paymentForecastDate = Carbon::createFromFormat('d/m/Y', $request->data_pagamento_efetuado);
        $paymentDate = Carbon::createFromFormat('d/m/Y', $request->data_pagamento_efetuado);

        $paymentAmount = bcdiv($request->valor_bruto, $request->nro_parcelas,2);

        $originalPayment = 0;
        for ($x = 1; $x <= $request->nro_parcelas; $x++){

            $payment = new Payments();
            $payment->nro_parcela = $x;
            $payment->data_prevista = $paymentForecastDate;
            $payment->data_pagamento_efetuado = $paymentDate;
            $payment->descricao = $request->descricao;
            $payment->valor_parcela = $paymentAmount;
            $payment->valor_bruto = $request->valor_bruto;
            $payment->valor_depois_taxa = $paymentAmount;
            $payment->forma_pagamento = $request->forma_pagamento;
            $payment->nro_parcelas = $request->nro_parcelas;
            $payment->alterado_por = $request->usuario;
            $payment->cliente = $request->cliente;
            $payment->taxa_cartao_utilizada = '0';

            if($request->forma_pagamento == 'Dinheiro'){
                $payment->data_pagamento_confirmado = $paymentDate;
                $payment->pago = 'SIM';
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

    // URL: /finances/conciliation
    public function conciliation(Request $request){

        $configList = ConfigController::getConfigForController();
        $pagamentos = DB::table('pagamentos')
            ->whereRaw('month(data_prevista) = ' . $request->mes
                . ' and year(data_prevista) = ' . $request->ano
                . ' and forma_pagamento = \'Cartão\'')->get();

        foreach ($pagamentos as $pagamento) {

            //Conciliation occurs only for non confirmed payments
            if($pagamento->data_pagamento_confirmado == null){

                $transaction = SumupController::getTransactionDetails($configList,$pagamento->id_transacao);
                //$sumup = "{\"id\": \"7dee114a-366d-4eb0-a7f2-4cbe1547b6c6\", \"transaction_code\": \"TV3SQ3SS9L\", \"merchant_code\": \"MFN22FKU\", \"username\": \"nathaly_biomedicina@hotmail.com\", \"amount\": 245, \"vat_amount\": 0, \"tip_amount\": 0, \"currency\": \"BRL\", \"timestamp\": \"2017-10-04T22:12:12.639Z\", \"lat\": -23.5311582857529, \"lon\": -47.4668258077557, \"horizontal_accuracy\": 65, \"status\": \"SUCCESSFUL\", \"payment_type\": \"POS\", \"simple_payment_type\": \"EMV\", \"entry_mode\": \"chip\", \"verification_method\": \"offline PIN\", \"card\": {\"last_4_digits\": \"3715\", \"type\": \"ELO\"}, \"local_time\": \"2017-10-04T22:12:12.639Z\", \"payout_date\": \"2017-10-31\", \"payout_plan\": \"TRUE_INSTALLMENT\", \"payout_type\": \"BANK_ACCOUNT\", \"installments_count\": 2, \"process_as\": \"CREDIT\", \"products\": [{\"name\": \"\", \"price\": 245, \"quantity\": 1, \"total_price\": 245 } ], \"vat_rates\": [], \"transaction_events\": [{\"id\": 59042731, \"event_type\": \"PAYOUT\", \"status\": \"PAID_OUT\", \"amount\": 117.72, \"due_date\": \"2017-12-03\", \"date\": \"2017-11-28\", \"installment_number\": 2, \"timestamp\": \"2017-10-04T22:12:40.360Z\"}, {\"id\": 59042730, \"event_type\": \"PAYOUT\", \"status\": \"PAID_OUT\", \"amount\": 117.72, \"due_date\": \"2017-11-03\", \"date\": \"2017-10-31\", \"installment_number\": 1, \"timestamp\": \"2017-10-04T22:12:40.356Z\"} ], \"simple_status\": \"SUCCESSFUL\", \"links\": [{\"rel\": \"receipt\", \"href\": \"https://receipts-ng.sumup.com/v0.1/receipts/7dee114a-366d-4eb0-a7f2-4cbe1547b6c6?mid=MFN22FKU&format=svg\", \"type\": \"image/svg+xml\"}, {\"rel\": \"receipt\", \"href\": \"https://receipts-ng.sumup.com/v0.1/receipts/7dee114a-366d-4eb0-a7f2-4cbe1547b6c6?mid=MFN22FKU&format=png\", \"type\": \"image/png\"}, {\"rel\": \"refund\", \"href\": \"https://api.sumup.com/v0.1/me/refund/7dee114a-366d-4eb0-a7f2-4cbe1547b6c6\", \"type\": \"application/json\", \"min_amount\": 245, \"max_amount\": 245, \"disclaimer\": \"settled_deduction\"} ], \"events\": [{\"id\": 59042731, \"transaction_id\": \"7dee114a-366d-4eb0-a7f2-4cbe1547b6c6\", \"type\": \"PAYOUT\", \"status\": \"PAID_OUT\", \"amount\": 117.72, \"timestamp\": \"2017-11-28T12:00:00.000Z\", \"fee_amount\": 4.78, \"installment_number\": 2, \"payout_type\": \"BANK_ACCOUNT\", \"payout_id\": 10293, \"deducted_amount\": 0, \"deducted_fee_amount\": 0 }, {\"id\": 59042730, \"transaction_id\": \"7dee114a-366d-4eb0-a7f2-4cbe1547b6c6\", \"type\": \"PAYOUT\", \"status\": \"PAID_OUT\", \"amount\": 117.72, \"timestamp\": \"2017-10-31T12:00:00.000Z\", \"fee_amount\": 4.78, \"installment_number\": 1, \"payout_type\": \"BANK_ACCOUNT\", \"payout_id\": 9971, \"deducted_amount\": 0, \"deducted_fee_amount\": 0 } ], \"payouts_received\": 2, \"payouts_total\": 2, \"location\": {\"lat\": -23.5311582857529, \"lon\": -47.4668258077557, \"horizontal_accuracy\": 65 }, \"tax_enabled\": true, \"auth_code\": \"624134\", \"internal_id\": 59379929 }";
                //$transaction = json_decode($sumup);

                Log::info('Payment data: '. json_encode($pagamento));
                Log::info('Sumup data: '. json_encode($transaction));

                foreach ($transaction->transaction_events as $parcela){

                    if($parcela->installment_number == $pagamento->nro_parcela && $parcela->status == 'PAID_OUT'){

                        $data_pagamento_confirmado = Carbon::createFromFormat('Y-m-d', $parcela->date);
                        $pagamento->data_pagamento_confirmado = $data_pagamento_confirmado->format('d/m/Y');
                        DB::table('pagamentos')->where('id', $pagamento->id)->update(['data_pagamento_confirmado' => $data_pagamento_confirmado]);
                    }
                }
            }
        }

        return response()->json(['result' => $pagamentos]);
    }
}
