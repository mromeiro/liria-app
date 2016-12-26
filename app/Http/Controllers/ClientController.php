<?php

namespace App\Http\Controllers;

use App\Clients;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;

class ClientController extends Controller
{
    /**
     * @param Request $request
     */
    public function create(Request $request) {

        $client = new Clients();

        //Dados Gerais
        $client->name = $request->name;

        if( $request->dataNascimento != null){
            $client->data_nascimento = Carbon::createFromFormat('d/m/Y', $request->dataNascimento);
        }

        $client->sobrenome = $request->sobrenome;
        $client->tel_fixo = $request->telFixo;
        $client->cpf = $request->cpf;
        $client->celular = $request->celular;
        $client->email = $request->email;

        $client->rua = $request->rua;
        $client->bairro = $request->bairro;
        $client->cidade = $request->cidade;
        $client->estado = $request->estado;
        $client->cep = $request->cep;
        $client->foto = $request->photoName;

        $client->obs = $request->obs;

        $client->save();

        return response()->json(['result' => $client]);
    }

    private function createTreatments(Request $request){

    }

    public function get(){
        return Response(Clients::all());
    }

    public function getClient(Request $request){

        $client = Clients::find($request->clientId);
        $client->treatments = Clients::find($request->clientId)->treatments;

        return  response()->json(['result' => $client]);
    }

    public function uploadPhoto(Request $request){

        $file = $request->file('file');

        //filename
        $today = Carbon::today()->format('dymhis');
        $fileNamePieces = explode(".", $file->getClientOriginalName());
        $fileName = $fileNamePieces[0] . '_' . $today . '.' . $fileNamePieces[1];

        //Save file
        $file->move('images', $fileName);

        //Return the naoe of the file
        return response()->json(['result' => $fileName]);
    }
}
