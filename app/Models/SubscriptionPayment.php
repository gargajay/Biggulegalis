<?php

namespace App\Models;

use App\Exceptions\PublicException;
use App\Helper\Stripe;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class SubscriptionPayment extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'subscription_id',
        'stripe_card_id',
        'total_users',
        'user_id_json',
        'per_user_price',
        'price',
        'payment_date',
        'payment_status',
        'currency',
        'payment_response_json'
    ];

    protected $hidden = [
        'user_id_json',
        'subscription_id',
        'stripe_card_id',
        'payment_response'
    ];

    protected $casts = [
        'subscription_id' => 'integer',
        'stripe_card_id' => 'integer',
        'total_users' => 'integer',
        'user_id_json' => 'array',
        'per_user_price' => 'float',
        'price' => 'float',
        'payment_date' => 'datetime',
        'payment_status' => 'integer',
        'currency' => 'string',
        'payment_response_json' => 'array',
    ];


    public static function createPayment(?int $companyId = null, ?array $userIds = null, ?int $subscriptionId = null)
    {
        $userObject = User::find($companyId);
        if (!$subscriptionId) {
            $subscriptionObject = Subscription::where('company_id', $companyId)->where('status', SUBSCRIPTION_STATUS['Active'])->latest()->with('subscription_plan')->first();
        } else {
            $subscriptionObject = Subscription::find($subscriptionId)->with('subscription_plan');
        }
        //create pending transaction
        $subscriptionPaymentObject = new SubscriptionPayment();
        $subscriptionPaymentObject->subscription_id = $subscriptionObject->id;
        $subscriptionPaymentObject->stripe_card_id = $userObject->primary_card_stripe_id;
        $subscriptionPaymentObject->total_users = count($userIds);
        $subscriptionPaymentObject->user_id_json = json_encode($userIds);
        $subscriptionPaymentObject->per_user_price = $subscriptionObject->subscription_plan->price;
        $subscriptionPaymentObject->price = count($userIds) * $subscriptionObject->subscription_plan->price;
        $subscriptionPaymentObject->payment_date = date('Y-m-d H:i:s');
        $subscriptionPaymentObject->payment_status = SUBSCRIPTION_PAYMENT_STATUS['Pending'];
        $subscriptionPaymentObject->currency = $subscriptionObject->subscription_plan->currency;
        // if data not save show error
        PublicException::NotSave($subscriptionPaymentObject->save());

        $data = [
            'subscription_payment_id' => $subscriptionPaymentObject->id,
            'amount' => $subscriptionObject->subscription_plan->price,
            'currency' => $subscriptionObject->subscription_plan->currency,
            'source' => $userObject->primary_card_stripe_id,
            'customer' => $userObject->stripe_id,
        ];
        $stripeResponse = Stripe::createCharge($data);
        if ($stripeResponse) {
            $subscriptionPaymentObject->payment_status = SUBSCRIPTION_PAYMENT_STATUS['Succeeded'];
            $subscriptionPaymentObject->payment_response_json = json_encode($stripeResponse);
            // if data not save show error
            PublicException::NotSave($subscriptionPaymentObject->save());


            $subscriptionObject->status = SUBSCRIPTION_STATUS['Active'];
            // if data not save show error
            PublicException::NotSave($subscriptionObject->save());
        }
    }

    public static function failedPayment(?int $subscriptionPaymentId, array $stripeResponse)
    {
        if ($subscriptionPaymentId) {
            $subscriptionPaymentObject = SubscriptionPayment::find($subscriptionPaymentId);
            if (!IsEmpty($subscriptionPaymentObject)) {
                $subscriptionPaymentObject->payment_status = SUBSCRIPTION_PAYMENT_STATUS['Failed'];
                $subscriptionPaymentObject->payment_response_json = json_encode($stripeResponse);
                // if data not save show error
                PublicException::NotSave($subscriptionPaymentObject->save());
            }
        }
    }
}
