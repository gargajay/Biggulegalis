<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Payment;
    use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Razorpay\Api\Api;

class RazorpayController extends Controller
{
    public function store(Request $request) {
        $input = $request->all();

        Log::info("paymet".json_encode($input));

        $api = new Api (config('settings.stripe.public_key'), config('settings.stripe.secret_key'));

        if(count($input) && !empty($input['razorpay_payment_id'])) {
            try {

            $payment = $api->payment->fetch($input['razorpay_payment_id']);

            log::info("Razorpay Payment fetched: ",['payment'=>$payment]);

            // Only capture if the status is 'authorized'
            if ($payment['status'] === 'authorized') {
                $response = $payment->capture(array('amount' => $payment['amount']));
            } else {
                // If already captured, use the existing payment object as the response
                $response = $payment;
            }
               // $response = $api->payment->fetch($input['razorpay_payment_id'])->capture(array('amount' => $payment['amount']));

                Log::info("Razorpay Response: ".json_encode($response));

                $payment = Payment::create([
                    'r_payment_id' => $response['id'],
                    'method' => $response['method'],
                    'currency' => $response['currency'],
                    'user_email' => $response['email'],
                    'user_id' => base64_decode($request->user_id),
                    'type' => 1,
                    'document_id'=>$request->document_id,
                    'amount' => $response['amount']/100,
                    'json_response' => json_encode((array)$response)
                ]);
            } catch(Exception $e) {

            Log::error("Razorpay Error: ".$e->getMessage());
               // return $e->getMessage();
                Session::flash('error', $e->getMessage());
                return redirect('payment?document_id=' . $request->document_id . '&u_id=' . $request->user_id);
            }
        }

        Session::flash('success',('Payment Successful'));
        return redirect('payment?document_id=' . $request->document_id . '&u_id=' . $request->user_id);
    }

    public function payment(Request $request){
        $data['user_id'] = $request->u_id;
        $data['document'] = Document::find($request->document_id);
        return view('web.payment',$data);
    }
}
