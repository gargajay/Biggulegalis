<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\Helper;
use App\Models\Payment;
use App\Models\User;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $data = ['page_title' => 'Payment', 'page_icon' => 'fa fa-money', 'table_url' => route('payment.data')];

        return view('web.payment.index', $data);
    }

    public function getData(Request $request)
    {
        // Define the data table structure
        $datatable = [
            'column' => [ 'option' => 'Option','sr_no' => 'S No.', 'users.full_name' => 'Name','transaction_id' => 'Transaction Id','amount' => 'Amount','currency' => 'Currency','payment_status' => 'Status' ],
            'search_column' => ['users.full_name','transaction_id','currency'],
            'order_column' => ['users.full_name','transaction_id','currency'],
        ];

        // Process the data table filters and options
        $datatableData = Helper::Datatable($request, $datatable, new Payment);


        // Get the total number of records (including deleted records)
        $totalRecords = Payment::withTrashed()->count();

        // Get the Size object and add search filter (if any)
        $paymentObject = Payment::withTrashed()->with('users');
        if ($datatableData['searchValue']) {
            $paymentObject->where(function ($query) use ($datatable, $datatableData) {
                foreach ($datatable['search_column'] as $columnName) {
                    if (strrpos($columnName, '.') !== false) {
                        $lastDotPosition = strrpos($columnName, '.');
                        $relation = substr($columnName, 0, $lastDotPosition);
                        $relationColumn = substr($columnName, $lastDotPosition + 1);
                        $query->orWhereHas($relation, function ($query) use ($datatableData, $relationColumn) {
                            $query->whereRaw($relationColumn . ' LIKE ?', ['%' . $datatableData['searchValue'] . '%']);
                        });
                    } else {
                        $query->orWhereRaw($columnName . ' LIKE ?', ['%' . $datatableData['searchValue'] . '%']);
                    }
                }
            });
            // Get the filtered number of records
            $filteredRecords = $paymentObject->count();
        } else {
            // Get the filtered number of records
            $filteredRecords = $totalRecords;
        }

        // dd($sizeObject->toSql());
        $start = $datatableData['start'] + 1;
        // Get the paginated and ordered data
        $datatableData = $paymentObject->skip($datatableData['start'])
            ->take($datatableData['length'])
            ->orderBy($datatableData['orderColumnName'], $datatableData['orderDirection'])

            ->get();
            // ->append('status');

        // Add options for each record in the DataTable

        foreach ($datatableData as &$value) {

            $value['sr_no'] = $start++;
            $value['option'] = optionButton('detail', route('payment.detail', ['id' => $value['id']]));

        }

        // Return the JSON response for DataTables
        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $datatableData,
        ]);
    }


    public function viewDetails(Request $request, int $id)
    {
        $data['paymentObject'] = Payment::withTrashed()->findOrFail($id)->with('users')->first();
        return view('web.payment.detail', $data);
    }

}
