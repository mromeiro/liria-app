<?php

namespace App\Http\Controllers;

use App\CardTax;
use App\Clients;
use App\ClientTreatments;
use App\Payments;
use App\Sessions;
use Illuminate\Http\Request;
use App\Treatments;
use Carbon\Carbon;

class TreatmentsController extends Controller
{
    public function get(Request $request) {

        $treatments = Treatments::all();
        return response()->json(['result' => $treatments]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createTreatmentForClient(Request $request){

        $clientTreament = new ClientTreatments();

        //Treatment data is received as a json inside the form data
        $tratamento_json = json_decode($request->tratamento);
        $clientTreament->nome = $tratamento_json->{'nome'};

        $clientTreament->cliente_id = $request->clienteId;
        $clientTreament->preco = $request->preco;

        if( $request->data_inicio != null){
            $clientTreament->data_inicio = Carbon::createFromFormat('d/m/Y', $request->data_inicio);
        }

        $clientTreament->desconto = $request->desconto;
        $clientTreament->forma_pagamento = $request->forma_pagamento;
        $clientTreament->nro_parcelas = $request->nro_parcelas;
        $clientTreament->nro_sessoes = $request->nro_sessoes;

        //Recover the correct tax for the treatment
        $pieces = explode(" ", $clientTreament->forma_pagamento);
        $cardTaxes = CardTax::whereRaw('nro_parcelas_inicio <= ? and nro_parcelas_fim >= ? and 
                           bandeira = ? and forma_pagamento = ?',
            [$clientTreament->nro_parcelas, $clientTreament->nro_parcelas, $pieces[0], $pieces[1]])->get();

        foreach ($cardTaxes as $cardTax)
        {
            $clientTreament->taxa_cartao_utilizada = $cardTax->taxa;
        }

        $clientTreament->save();

        //Create sessions
        $this->createSessions($clientTreament);

        //Create payments
        $this->createPayments($clientTreament, $pieces[1]);

        return response()->json(['result' => $clientTreament]);
    }

    private function createPayments(ClientTreatments $clientTreatments, $formaPagamento){

        $today = Carbon::today();

        if($formaPagamento == 'Débito'){
            $paymentDate = $today->addDays(5);
        }else{
            $paymentDate = $today->addDays(30);
        }

        for ($x = 1; $x <= $clientTreatments->nro_parcelas; $x++){

            $payment = new Payments();
            $payment->nro_parcela = $x;
            $payment->tratamento_cliente_id = $clientTreatments->id;
            $payment->data_prevista = $paymentDate;

            $paymentAmount = bcsub($clientTreatments->preco, $clientTreatments->desconto);
            $percentage = bcsub('1',strval($clientTreatments->taxa_cartao_utilizada),4);
            $paymentAmount = bcmul($paymentAmount,$percentage,2);
            $paymentAmount = bcdiv($paymentAmount, $clientTreatments->nro_parcelas,2);
            $payment->valor_parcela = $paymentAmount;

            $payment->save();

            $paymentDate->addDays(30);
        }
    }

    private function createSessions(ClientTreatments $clientTreatments){

        for ($x = 1; $x <= $clientTreatments->nro_sessoes; $x++){

            $session = new Sessions();
            $session->tratamento_cliente_id = $clientTreatments->id;
            $session->nro_sessao = $x;

            $session->save();
        }

    }
}
