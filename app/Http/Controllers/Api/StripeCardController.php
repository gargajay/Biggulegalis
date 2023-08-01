<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Exceptions\PublicException;
use App\Helper\Helper;
use App\Helper\Stripe;
use App\Models\StripeCard;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StripeCardController extends Controller
{
    public function __construct(Request $request)
    {
        // make card expiry month without front zero
        updateRequestValue('card_expiry_month', (int) $request->card_expiry_month);
    }


    public function createStripeCard(Request $request)
    {
        // validate rules for input
        $rules = [
            'name_on_card' => 'required|string|max:255',
            'card_number' => 'required|numeric|positive_integer|digits_between:13,19',
            'card_expiry_month' => 'required|numeric|positive_integer|min:1|max:12',
            'card_expiry_year' => 'required|numeric|positive_integer|digits:2|min:' . date('y'),
            'card_cvv' => 'required|numeric|positive_integer|digits_between:3,4',
        ];

        // validate input data using the Validator method of the PublicException class
        PublicException::Validator($request->all(), $rules);

        // Begin database transaction
        DB::beginTransaction();

        $userObject = User::find(Auth::id());
        if (!$userObject->stripe_id) {
            $stripeToken = Stripe::createToken($request->all());
            $userObject->stripe_id = Stripe::createCustomer(['name' => Auth::user()->full_name, 'email' => $userObject->email, 'source' => $stripeToken->id])->id;
            // if data not save show error
            PublicException::NotSave($userObject->save());
        }

        $stripeToken = Stripe::createToken($request->all());

        // add stripe card
        $stripeCardObject = new StripeCard();
        $stripeCardObject->user_id = Auth::id();
        $stripeCardObject->stripe_card_id = Stripe::createCard(['user_stripe_id' => $userObject->stripe_id, 'token' => $stripeToken->id])->id;
        $stripeCardObject->name_on_card = $request->name_on_card;
        $stripeCardObject->card_last_four = substr($request->card_number, -4);
        $stripeCardObject->digit_length = strlen($request->card_number);
        $stripeCardObject->card_expiry_month = $request->card_expiry_month;
        $stripeCardObject->card_expiry_year = $request->card_expiry_year;
        $stripeCardObject->is_active = StripeCard::where('user_id', Auth::id())->where('is_active', true)->count() ? false : true;

        // if data not save show error
        PublicException::NotSave($stripeCardObject->save());

        return Helper::SuccessReturn($stripeCardObject, 'CREATE_STRIPE_CARD');
    }


    public function setPrimaryStripeCard(Request $request)
    {
        // validate rules for input
        $rules = [
            'stripe_card_id' => ['required', 'numeric', 'positive_integer', 'digits_between:1,18', 'iexists:stripe_cards,id,user_id,' . Auth::id()],
        ];

        // validate input data using the Validator method of the PublicException class
        PublicException::Validator($request->all(), $rules);

        // Begin database transaction
        DB::beginTransaction();

        //make previous card inactive
        $stripeCardObject = StripeCard::where('user_id', Auth::id());
        if (!IsEmpty($stripeCardObject)) {
            $stripeCardObject->update(['is_active' => false]);
        }

        //make this card active
        $stripeCardObject = StripeCard::findOrFail($request->stripe_card_id);
        $stripeCardObject->is_active = true;

        // if data not save show error
        PublicException::NotSave($stripeCardObject->save());

        return Helper::SuccessReturn($stripeCardObject, 'PRIMARY_STRIPE_CARD');
    }


    public function deleteStripeCard(Request $request)
    {
        // validate rules for input
        $rules = [
            'stripe_card_id' => ['required', 'numeric', 'positive_integer', 'digits_between:1,18', 'iexists:stripe_cards,id,user_id,' . Auth::id()],
        ];

        // validate input data using the Validator method of the PublicException class
        PublicException::Validator($request->all(), $rules);

        // Begin database transaction
        DB::beginTransaction();

        $stripeCardCount = StripeCard::where('user_id', Auth::id())->count();

        $stripeCardObject = StripeCard::findOrFail($request->stripe_card_id);
        if ($stripeCardCount <= 1 || $stripeCardObject->is_active == false) {
            $stripeCardObject->delete();
        } else {
            PublicException::Error('PRIMARY_OTHER_STRIPE_CARD');
        }
        return Helper::SuccessReturn($stripeCardObject, 'DELETE_STRIPE_CARD');
    }

    public function getStripeCard(Request $request)
    {
        // validate rules for input
        $rules = [
            'stripe_card_id' => ['nullable', 'numeric', 'positive_integer', 'digits_between:1,18', 'iexists:stripe_cards,id,user_id,' . Auth::id()],
        ];

        // validate input data using the Validator method of the PublicException class
        PublicException::Validator($request->all(), $rules);

        $stripeCardObject = StripeCard::where('user_id', Auth::id())->latest();

        if ($request->stripe_card_id) {
            $stripeCardObject->where('id', $request->stripe_card_id);
            $stripeCardObject = $stripeCardObject->first();
            if (!IsEmpty($stripeCardObject)) {
                $stripeCardObject = $stripeCardObject->append('card_digit');
            }
        } else {
            $stripeCardObject = paginate($stripeCardObject, append: ['card_digit']);
        }

        return Helper::SuccessReturn($stripeCardObject, 'GET_STRIPE_CARD');
    }
}
