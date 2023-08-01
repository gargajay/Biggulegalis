<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\Helper;
use App\Exceptions\PublicException;
use App\Models\WorkoutHours;

class WorkoutHoursController extends Controller
{
    public function index(Request $request)
    {
        $data = ['page_title' => 'Workout Hours', 'page_icon' => 'fa-user', 'table_url' => route('workout-hours.data')];

        return view('web.workout-hours.index', $data);
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
        $workoutHoursObject = WorkoutHours::withTrashed();
        // Process the data table filters and options
        $datatableData = Helper::Datatable($datatable, $workoutHoursObject);
        // Get the paginated and ordered data
        $workoutHoursData = $datatableData['modelObject']->get()->append('status')->makeVisible('created_at')->toArray();
        $srno = $datatableData['start'];

        // Add options for each record in the DataTable
        foreach ($workoutHoursData as &$row) {
            $row['srno'] = ++$srno;
            // Add edit, status, and delete buttons for the record
            $row['option'] = optionButton('edit', route('workout-hours.form', ['id' => $row['id']]));
            $row['option'] .= optionButton('status', route('workout-hours.status', ['id' => $row['id']]), $row);
            $row['option'] .= optionButton('delete', route('workout-hours.delete', ['id' => $row['id']]));

            $row['created_at'] = formatDateWithTimezone($row['created_at']);
        }

        // Return the JSON response for DataTables
        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => $datatableData['totalRecords'],
            'recordsFiltered' => $datatableData['filteredRecords'],
            'data' => $workoutHoursData,
        ]);
    }
    public function form(Request $request, int $id = null)
    {
        $data['workoutHoursObject'] = $id ? WorkoutHours::withTrashed()->findOrFail($id) : new WorkoutHours;
        return view('web.workout-hours.form', $data);
    }
    public function formSave(Request $request, int $id = null)
    {
        $rules = [
            'name' => 'required|string|max:255|iunique:workout_hours,name,' . $id,
        ];

        // Validate the user input data
        PublicException::Validator($request->all(), $rules);

        $workoutHoursObject = $id ? WorkoutHours::withTrashed()->findOrFail($id) : new WorkoutHours;
        $workoutHoursObject->name = $request->name;

        // if data not save show error
        PublicException::NotSave($workoutHoursObject->save());

        // return a success response with the WorkoutHours data
        return Helper::SuccessReturn([], 'RECORD_SAVED');
    }
    public function changeStatus(Request $request, int $id)
    {
        $workoutHoursObject = WorkoutHours::withTrashed()->findOrFail($id);
        if ($workoutHoursObject->deleted_at === null) {
            $workoutHoursObject->delete();
            // return a success response with the WorkoutHours data
            return Helper::SuccessReturn([], 'RECORD_INACTIVE');
        } else {
            $workoutHoursObject->restore();
            // return a success response with the WorkoutHours data
            return Helper::SuccessReturn([], 'RECORD_ACTIVE');
        }
    }

    public function deleteRow(Request $request, int $id)
    {
        $workoutHoursObject = WorkoutHours::withTrashed()->findOrFail($id);
        $workoutHoursObject->forceDelete();
        return Helper::SuccessReturn([], 'RECORD_DELETE');
    }

    }
