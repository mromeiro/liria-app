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
    public function createTreatmentForClient(Request $request)
    {

        $clientTreament = new ClientTreatments();

        $clientTreament->alterado_por = $request->usuario;

        //Treatment data is received as a json inside the form data
        $clientTreament->nome = $request->tratamento;

        $clientTreament->cliente_id = $request->clienteId;

        if ($request->data_inicio != null) {
            $clientTreament->data_inicio = Carbon::createFromFormat('d/m/Y', $request->data_inicio);
        }

        if($request->nro_sessoes == null){
            $clientTreament->nro_sessoes = 1;
        }else{
            $clientTreament->nro_sessoes = $request->nro_sessoes;
        }

        $clientTreament->save();

        return response()->json(['result' => $clientTreament]);
    }



    public function updateTreatment(Request $request){

        $clientTreament = ClientTreatments::find($request->id);

        if ($request->data_inicio != null) {
            $clientTreament->data_inicio = Carbon::createFromFormat('d/m/Y', $request->data_inicio);
        }

        $clientTreament->preco = $request->preco;
        $clientTreament->desconto = $request->desconto;
        $clientTreament->forma_pagamento = $request->forma_pagamento;
        $clientTreament->nro_parcelas = $request->nro_parcelas;
        $clientTreament->nro_sessoes = $request->nro_sessoes;
        $clientTreament->alterado_por = $request->usuario;

        $clientTreament->update();

        //Recover the correct tax for the treatment
        if ($request->forma_pagamento == "Dinheiro" || $request->forma_pagamento == "Cheque" || $request->forma_pagamento == null) {

            $clientTreament->taxa_cartao_utilizada = 0;
            $clientTreament->forma_pagamento = $request->forma_pagamento;

        } else {

            $pieces = explode(" ", $request->forma_pagamento);
            $bandeira = $pieces[0];
            $clientTreament->forma_pagamento = $pieces[1];

            $cardTaxes = CardTax::whereRaw('nro_parcelas_inicio <= ? and nro_parcelas_fim >= ? and 
                           bandeira = ? and forma_pagamento = ?',
                [$clientTreament->nro_parcelas, $clientTreament->nro_parcelas, $bandeira, $clientTreament->forma_pagamento])->get();

            foreach ($cardTaxes as $cardTax) {
                $clientTreament->taxa_cartao_utilizada = $cardTax->taxa;
            }
        }

        $paymentAmount = bcsub($clientTreament->preco, $clientTreament->desconto);
        $percentage = bcsub('1',strval($clientTreament->taxa_cartao_utilizada),4);
        $clientTreament->preco_final = bcmul($paymentAmount,$percentage,2);

        $clientTreament->save();

        //Create sessions
        Sessions::where('tratamento_cliente_id',$clientTreament->id)->delete();
        $this->createSessions($clientTreament, $request->usuario);

        //Create payments
        Payments::where('tratamento_cliente_id',$clientTreament->id)->delete();
        $this->createPayments($clientTreament, $clientTreament->forma_pagamento, $request->usuario);

        return response()->json(['result' => $clientTreament]);
    }
}
