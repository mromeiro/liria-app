<?php

namespace App\Http\Controllers;

use App\Payments;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PaymentsController extends Controller
{

    public function updatePaymentDate(Request $request) {

        $payment = Payments::find($request->paymentId);

        if( $request->data_pagamento != null){
            $payment->data_pagamento = Carbon::createFromFormat('d/m/Y', $request->data_pagamento);
        }

        $payment->update();
        return response()->json(['result' => $payment]);
    }
}
