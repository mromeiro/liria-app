<?php

namespace App\Http\Controllers;

use App\Clients;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    /**
     * @param Request $request
     */
    public function create(Request $request) {

        $client = new Clients();

        //Dados Gerais
        $client->name = $request->name;

        if( $request->data_nascimento != null){
            $client->data_nascimento = Carbon::createFromFormat('d/m/Y', $request->data_nascimento);
        }

        $client->sobrenome = $request->sobrenome;
        $client->tel_fixo = $request->tel_fixo;
        $client->cpf = $request->cpf;
        $client->celular = $request->celular;
        $client->email = $request->email;

        $client->rua = $request->rua;
        $client->bairro = $request->bairro;
        $client->cidade = $request->cidade;
        $client->estado = $request->estado;
        $client->cep = $request->cep;
        $client->foto = $request->photoName;
        $client->alterado_por = $request->usuario;

        $client->obs = $request->obs;

        $client->save();

        return response()->json(['result' => $client]);
    }

    public function update(Request $request) {

        $client = Clients::find($request->id);

        //Dados Gerais
        $client->name = $request->name;

        if( $request->data_nascimento != null){
            $client->data_nascimento = Carbon::createFromFormat('d/m/Y', $request->data_nascimento);
        }

        $client->sobrenome = $request->sobrenome;
        $client->tel_fixo = $request->tel_fixo;
        $client->cpf = $request->cpf;
        $client->celular = $request->celular;
        $client->email = $request->email;

        $client->rua = $request->rua;
        $client->bairro = $request->bairro;
        $client->cidade = $request->cidade;
        $client->estado = $request->estado;
        $client->cep = $request->cep;
        $client->foto = $request->photoName;
        $client->alterado_por = $request->usuario;

        $client->obs = $request->obs;

        $client->update();

        return response()->json(['result' => $client]);
    }

    public function get(){
        return Response(Clients::all());
    }

    public function getClient(Request $request){

        $client = Clients::with('treatments')->find($request->clientId);
         return  response()->json(['result' => $client]);
    }

    public function getClientByNameOrCpf(Request $request){

        $clients = Clients::where('name', 'like', '%' . $request->searchString . '%')
                            ->orWhere('sobrenome', 'like', '%' . $request->searchString . '%')
                            ->orWhere('cpf', $request->searchString)->get();

        return  response()->json(['result' => $clients]);
    }

    public function uploadPhoto(Request $request){

        $file = $request->file('file');

        //filename
        $today = Carbon::now()->format('dymhis');
        $fileNamePieces = explode(".", $file->getClientOriginalName());
        $fileName = $fileNamePieces[0] . '_' . $today . '.' . $fileNamePieces[1];

        //Save file
        $file->move('images/clientes', $fileName);

        //Return the name of the file
        return response()->json(['result' => $fileName]);
    }

    public function getBirthdaysClient(Request $request){

        $clients = DB::table('clientes') //
            ->select(DB::raw('name, sobrenome, foto, DATE_FORMAT(data_nascimento,\'%d/%m\') as dia_aniversario, DATE_FORMAT(data_nascimento,\'%d\') as dia_mes'))
            ->whereRaw('month(data_nascimento) = ' . $request->mes)
            ->orderByRaw('day(data_nascimento)', 'asc')
            ->get();

        return response()->json(['result' => $clients]);
    }
}
