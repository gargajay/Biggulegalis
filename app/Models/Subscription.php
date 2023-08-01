<?php

namespace App\Models;

use App\Exceptions\PublicException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'subscription_plan_id',
        'company_id',
        'start_date',
        'end_date',
        'status'
    ];

    protected $hidden = [
        'subscription_plan_id',
        'company_id'
    ];

    protected $casts = [
        'subscription_plan_id' => 'integer',
        'company_id' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'status' => 'integer'
    ];


    public function subscription_plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }


    public static function subscribePlan($userId)
    {
        $userObject = User::find($userId);
        $subscriptionObjectCount = Subscription::where('company_id', $userId)->count();
        //subscribe free plan if no plan exist
        if (!$subscriptionObjectCount) {
            $subscriptionPlanObject = SubscriptionPlan::where('category', SUBSCRIPTION_PLAN_CATEGORY['Free Plan'])->first();
            $subscriptionObject = new Subscription;
            $subscriptionObject->subscription_plan_id = $subscriptionPlanObject->id;
            $subscriptionObject->company_id = $userId;
            $subscriptionObject->start_date = date('Y-m-d H:i:s');
            $subscriptionObject->end_date = Carbon::now()->add($subscriptionPlanObject->duration, strtolower(array_flip(SUBSCRIPTION_PLAN_INTERVAL)[$subscriptionPlanObject->interval]))->format('Y-m-d H:i:s');
            $subscriptionObject->status = SUBSCRIPTION_STATUS['Active'];
            // if data not save show error
            PublicException::NotSave($subscriptionObject->save());
        }

        //check active plan is free or premium
        $subscriptionObject = Subscription::where('company_id', $userId)->where('status', SUBSCRIPTION_STATUS['Active'])->where('start_date', '<=', Carbon::now())->where('end_date', '>=', Carbon::now())->latest()->with('subscription_plan')->first();

        //skip process if free plan is work else subscripe premium plan and cut charges
        // if (IsEmpty($subscriptionObject) || (!IsEmpty($subscriptionObject) && $subscriptionObject->subscription_plan->category != SUBSCRIPTION_PLAN_CATEGORY['Free Plan'])) {
        //     if (!IsEmpty($subscriptionObject)) {
        //         $subscriptionObject->status = SUBSCRIPTION_STATUS['Canceled'];
        //         // if data not save show error
        //         PublicException::NotSave($subscriptionObject->save());
        //     }

        //     $subscriptionPlanObject = SubscriptionPlan::find($userObject->default_subscription_plan_id);

        //     $subscriptionObject = new Subscription;
        //     $subscriptionObject->subscription_plan_id = $userObject->default_subscription_plan_id;
        //     $subscriptionObject->company_id = $userId;
        //     $subscriptionObject->start_date = date('Y-m-d H:i:s');
        //     $subscriptionObject->end_date = Carbon::now()->add($subscriptionPlanObject->duration, strtolower(array_flip(SUBSCRIPTION_PLAN_INTERVAL)[$subscriptionPlanObject->interval]))->format('Y-m-d H:i:s');
        //     $subscriptionObject->status = SUBSCRIPTION_STATUS['Not Active'];

        //     // if data not save show error
        //     PublicException::NotSave($subscriptionObject->save());

        //     $employeeUserIds = User::orWhere(['id' => $userId, 'parent_user_id' => $userId])->pluck('id')->toArray();

        //     //SubscriptionPayment::createPayment($userId, $employeeUserIds, $subscriptionObject->id);
        // }
    }
}
