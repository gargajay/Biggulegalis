<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use App\Helper\Helper;
use App\Exceptions\PublicException;

use Illuminate\Http\Request;

class GoalController extends Controller
{
    public function index(Request $request)
    {
        $data = ['page_title' => 'Goals', 'page_icon' => 'fa-user', 'table_url' => route('goal.data')];

        return view('web.goal.index', $data);
    }
    /**
     * Get data for DataTables.
     */
    public function getData(Request $request)
    {
        // Define the data table structure
        $datatable = [
            'column' => ['option' => 'Option', 'srno' => 'S No.', 'name' => 'Name', 'status' => 'Status'],
            'search_column' => ['name'],
            'order_column' => ['name'],
        ];

        // Process the data table filters and options
        $goalObject = Goal::withTrashed();
        // Process the data table filters and options
        $datatableData = Helper::Datatable($datatable, $goalObject);
        // Get the paginated and ordered data
        $goalData = $datatableData['modelObject']->get()->append('status')->makeVisible('created_at')->toArray();
        $srno = $datatableData['start'];

        // Add options for each record in the DataTable
        foreach ($goalData as &$row) {
            $row['srno'] = ++$srno;
            // Add edit, status, and delete buttons for the record
            $row['option'] = optionButton('edit', route('goal.form', ['id' => $row['id']]));
            $row['option'] .= optionButton('status', route('goal.status', ['id' => $row['id']]), $row);
            $row['option'] .= optionButton('delete', route('goal.delete', ['id' => $row['id']]));

            $row['created_at'] = formatDateWithTimezone($row['created_at']);
        }

        // Return the JSON response for DataTables
        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => $datatableData['totalRecords'],
            'recordsFiltered' => $datatableData['filteredRecords'],
            'data' => $goalData,
        ]);
    }

    public function form(Request $request, int $id = null)
    {
        $data['goalObject'] = $id ? Goal::withTrashed()->findOrFail($id) : new Goal;
        return view('web.goal.form', $data);
    }
    public function formSave(Request $request, int $id = null)
    {
        $rules = [
            'name' => 'required|string|max:255|iunique:goals,name,' . $id,
        ];

        // Validate the user input data
        PublicException::Validator($request->all(), $rules);

        $goalObject = $id ? Goal::withTrashed()->findOrFail($id) : new Goal;
        $goalObject->name = $request->name;

        // if data not save show error
        PublicException::NotSave($goalObject->save());

        // return a success response with the goal data
        return Helper::SuccessReturn([], 'RECORD_SAVED');
    }
    public function changeStatus(Request $request, int $id)
    {
        $goalObject = Goal::withTrashed()->findOrFail($id);
        if ($goalObject->deleted_at === null) {
            $goalObject->delete();
            // return a success response with the goal data
            return Helper::SuccessReturn([], 'RECORD_INACTIVE');
        } else {
            $goalObject->restore();
            // return a success response with the goal data
            return Helper::SuccessReturn([], 'RECORD_ACTIVE');
        }
    }

    public function deleteRow(Request $request, int $id)
    {
        $goalObject = Goal::withTrashed()->findOrFail($id);
        $goalObject->forceDelete();
        return Helper::SuccessReturn([], 'RECORD_DELETE');
    }
}
