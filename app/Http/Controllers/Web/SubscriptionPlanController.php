<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\PublicException;
use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionPlanController extends Controller
{
    public function index(Request $request)
    {
        $data = ['page_title' => 'Subscription Plan', 'page_icon' => 'fa-money', 'table_url' => route('subscription-plan.data')];
        return view('web.subscription-plan.index', $data);
    }

    /**
     * Get data for DataTables.
     */
    public function getData(Request $request)
    {
        // Define the data table structure
        $datatable = [
            'column' => ['option' => 'Option', 'srno' => 'Sr. No.', 'category' => 'Category', 'name' => 'Name', 'min_users' => 'Min Users',  'max_users' => 'Max Users', 'duration' => 'Duration', 'price' => 'Price', 'currency' => 'Currency', 'sort_order' => 'Display Order', 'created_at' => 'Create Date', 'status' => 'Status'],
            'search_column' => ['name', 'price', 'created_at'],
            'order_column' => ['name', 'price', 'created_at'],
        ];

        // Get the model object with trashed records
        $subscriptionPlanObject = SubscriptionPlan::withTrashed();

        // Process the data table filters and options
        $datatableData = Helper::Datatable($datatable, $subscriptionPlanObject);

        // Get the paginated and ordered data
        $subscriptionPlanData = $datatableData['modelObject']->get()->append('status')->makeVisible(['created_at', 'sort_order', 'stripe_plan_id'])->toArray();

        $srno = $datatableData['start'];

        // Add options for each record in the DataTable
        foreach ($subscriptionPlanData as &$row) {
            $row['srno'] = ++$srno;
            // Add edit, status, and delete buttons for the record
            $row['option'] = optionButton('edit', route('subscription-plan.form', ['id' => $row['id']]));
            $row['option'] .= optionButton('status', route('subscription-plan.status', ['id' => $row['id']]), $row);
            $row['option'] .= optionButton('delete', route('subscription-plan.delete', ['id' => $row['id']]));

            $row['category'] = array_flip(SUBSCRIPTION_PLAN_CATEGORY)[$row['category']] ?? $row['category'];
            $row['currency'] = array_flip(SUBSCRIPTION_CURRENCIES)[$row['currency']] ?? $row['currency'];
            $row['duration'] = $row['duration'] . ' ' . (array_flip(SUBSCRIPTION_PLAN_INTERVAL)[$row['interval']] ?? $row['interval']);
            $row['max_users'] = $row['max_users'] == 0 ? 'Unlimited' : $row['max_users'];
            $row['created_at'] = formatDateWithTimezone($row['created_at']);
        }

        // Return the JSON response for DataTables
        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => $datatableData['totalRecords'],
            'recordsFiltered' => $datatableData['filteredRecords'],
            'data' => $subscriptionPlanData,
        ]);
    }

    public function form(Request $request, int $id = null)
    {
        $data['subscriptionPlanObject'] = $id ? SubscriptionPlan::withTrashed()->findOrFail($id) : new SubscriptionPlan;
        return view('web.subscription-plan.form', $data);
    }

    public function formSave(Request $request, int $id = null)
    {
        $rules = [
            'category' => ['required', 'numeric', 'positive_integer', 'in:' . implode(',', SUBSCRIPTION_PLAN_CATEGORY), 'iunique:subscription_plans,category,category,' . SUBSCRIPTION_PLAN_CATEGORY['Free Plan'].','.$id],
            'name' => ['required', 'iunique:subscription_plans,name,' . $id, 'max:255'],
            'min_users' => ['required', 'numeric', 'positive_integer', 'digits_between:1,18'],
            'max_users' => ['required', 'numeric', 'positive_integer', 'digits_between:1,18'],
            'duration' => ['required', 'numeric', 'positive_integer', 'digits_between:1,18'],
            'interval' => ['required', 'numeric', 'positive_integer', 'in:' . implode(',', SUBSCRIPTION_PLAN_INTERVAL)],
            'price' => ['required', 'numeric', 'positive_decimal'],
            'currency' => ['required',  'in:' . implode(',', SUBSCRIPTION_CURRENCIES)],
            'sort_order' => ['nullable', 'numeric', 'positive_integer', 'digits_between:1,18'],
        ];

        // Validate the user input data
        PublicException::Validator($request->all(), $rules);

        $subscriptionPlanObject = $id ? SubscriptionPlan::withTrashed()->findOrFail($id) : new SubscriptionPlan;
        $subscriptionPlanObject = Helper::UpdateObjectIfKeyNotEmpty($subscriptionPlanObject, [
            'category',
            'name',
            'min_users',
            'max_users',
            'duration',
            'interval',
            'price',
            'currency',
            'sort_order'
        ]);


        // if data not save show error
        PublicException::NotSave($subscriptionPlanObject->save());


        return Helper::SuccessReturn([], 'RECORD_SAVED');
    }

    public function changeStatus(Request $request, int $id)
    {
        $subscriptionPlanObject = SubscriptionPlan::withTrashed()->findOrFail($id);
        if ($subscriptionPlanObject->deleted_at === null) {
            $subscriptionPlanObject->delete();

            return Helper::SuccessReturn([], 'RECORD_INACTIVE');
        } else {
            $subscriptionPlanObject->restore();

            return Helper::SuccessReturn([], 'RECORD_ACTIVE');
        }
    }

    public function deleteRow(Request $request, int $id)
    {
        $subscriptionPlanObject = SubscriptionPlan::withTrashed()->findOrFail($id);
        $subscriptionPlanObject->forceDelete();
        return Helper::SuccessReturn([], 'RECORD_DELETE');
    }
}
