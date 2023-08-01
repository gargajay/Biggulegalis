<?php

namespace App\Helper;

use App\Exceptions\PublicException;
use App\Models\SubscriptionPayment;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\Exception\CardException;
use Stripe\Exception\RateLimitException;
use Stripe\Refund;
use Stripe\Stripe as StripeGateway;
use Stripe\Token;

class Stripe
{
    protected static $subscriptionPaymentId;

    public static function setApiKey()
    {
        // Check if stripe credentials are provided.
        if (!config('settings.stripe.secret_key') || !config('settings.stripe.public_key')) {
            PublicException::Error('STRIPE_CREDENTIALS');
        }
        StripeGateway::setApiKey(config('settings.stripe.secret_key'));
    }

    public static function createCustomer(array $data)
    {
        self::setApiKey();
        // create a new customer on Stripe
        return Customer::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'source' => $data['source'],
        ]);
    }


    public static function createToken(array $card)
    {
        self::setApiKey();
        // Create a token with card details
        return Token::create([
            'card' => [
                'number' => $card['card_number'],
                'exp_month' => $card['card_expiry_month'],
                'exp_year' => $card['card_expiry_year'],
                'cvc' => $card['card_cvv'],
            ]
        ]);
    }

    public static function createCard(array $data)
    {
        self::setApiKey();
        // Create a new card
        $customer = Customer::retrieve($data['user_stripe_id']);
        if ($customer->sources) {
            return $customer->createSource(['source' => $data['token']]);
        } else {
            return Customer::createSource($data['user_stripe_id'], ['source' => $data['token']]);
        }
    }

    public static function createCharge(array $data)
    {
        self::setApiKey();
        self::$subscriptionPaymentId = $data['subscription_payment_id'] ?? null;
        return Charge::create([
            'amount' => (int) ($data['amount'] * 100),
            'currency' => strtolower($data['currency']),
            'source' => $data['stripe_card_id'],
            'customer' => $data['stripe_id'],
        ]);
    }

    public static function createRefund(array $data)
    {
        self::setApiKey();
        return Refund::create([
            'charge' => $data['payment_id'],
            'reason' => $data['reason'],
        ]);
    }

    public static function stripeException($exception)
    {
        $exceptionTypes = [
            CardException::class,
            RateLimitException::class,
            InvalidRequestException::class,
            AuthenticationException::class,
            ApiConnectionException::class,
            ApiErrorException::class,
        ];

        if (in_array(get_class($exception), $exceptionTypes)) {
            $errorCode = $exception->getError()->code;
            //SubscriptionPayment::failedPayment(self::$subscriptionPaymentId, ['errorCode' => $exception->getError()->code, 'message' => $exception->getMessage()]);
            $key = 'stripe.' . $errorCode;
            $message = trans($key) === $key ? $exception->getMessage() : trans($key);
            return ['success' => FALSE, 'status' => STATUS_OK, 'message' => $message];
        }
        return null;
    }
}
