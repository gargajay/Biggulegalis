<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\PublicException;
use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(Request $request)
    {
        // make email lower case in request
        updateRequestValue('email', strtolower($request->email));
    }


    public function index(Request $request)
    {
        $data = ['page_title' => 'Users', 'page_icon' => 'fa-user', 'table_url' => route('user.data')];

        return view('web.user.index', $data);
    }

    /**
     * Get data for DataTables.
     */
    public function getData(Request $request)
    {
        // Define the data table structure
        $datatable = [
            'column' => ['option' => 'Option', 'srno' => 'Sr. No.', 'full_name' => 'Full Name', 'email' => 'Email', 'created_at' => 'Create Date', 'status' => 'Status'],
            'search_column' => ['full_name', 'email', 'created_at'],
            'order_column' => ['full_name', 'email', 'created_at'],
        ];

        // Get the model object with trashed records
        $userObject = User::where('user_type', USER_TYPE['USER']);

        // Process the data table filters and options
        $datatableData = Helper::Datatable($datatable, $userObject);

        // Get the paginated and ordered data
        $userData = $datatableData['modelObject']->get()->append('status')->makeVisible('created_at')->toArray();

        $srno = $datatableData['start'];

        // Add options for each record in the DataTable
        foreach ($userData as &$row) {
            $row['srno'] = ++$srno;

            // Add edit, status, and delete buttons for the record
            $row['option'] = optionButton('detail', route('user.detail', ['id' => $row['id']]));
            // $row['option'] .= optionButton('status', route('user.status', ['id' => $row['id']]), $row);
            $row['option'] .= optionButton('delete', route('user.delete', ['id' => $row['id']]));

            $row['created_at'] = formatDateWithTimezone($row['created_at']);
        }

        // Return the JSON response for DataTables
        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => $datatableData['totalRecords'],
            'recordsFiltered' => $datatableData['filteredRecords'],
            'data' => $userData,
        ]);
    }

    public function changeStatus(Request $request, int $id)
    {
        $userObject = User::withTrashed()->findOrFail($id);
        if ($userObject->deleted_at === null) {
            $userObject->delete();

            return Helper::SuccessReturn([], 'RECORD_INACTIVE');
        } else {
            $userObject->restore();

            return Helper::SuccessReturn([], 'RECORD_ACTIVE');
        }
    }

    public function deleteRow(Request $request, int $id)
    {
        $userObject = User::withTrashed()->findOrFail($id);
        $userObject->forceDelete();
        return Helper::SuccessReturn([], 'RECORD_DELETE');
    }


    public function viewDetails(Request $request, int $id)
    {
        $data['userObject'] = User::withTrashed()->findOrFail($id);
        return view('web.user.detail', $data);
    }
}
