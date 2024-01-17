<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Association;
use App\Helper\Helper;
use App\Exceptions\PublicException;

use Illuminate\Http\Request;

class AssociationController extends Controller
{
    public function index(Request $request)
    {
        $data = ['page_title' => 'Associations', 'page_icon' => 'fa-user', 'table_url' => route('association.data')];

        return view('web.association.index', $data);
    }
    /**
     * Get data for DataTables.
     */
    public function getData(Request $request)
    {
        // Define the data table structure
        $datatable = [
            'column' => ['option' => 'Option', 'srno' => 'S No.', 'name' => 'Name', 'status' => 'Status','type'=>'Association Type','parent'=>'Parent','permission_type'=>'Permisson Type'],
            'search_column' => ['name'],
            'order_column' => ['name'],
        ];

        // Process the data table filters and options
        $associationObject = Association::withTrashed();
        // Process the data table filters and options
        $datatableData = Helper::Datatable($datatable, $associationObject,['association_type', 'asc']);
        // Get the paginated and ordered data
        $associationData = $datatableData['modelObject']->get()->append(['parent','type'])->makeVisible('created_at')->toArray();
        $srno = $datatableData['start'];

        // Add options for each record in the DataTable
        foreach ($associationData as &$row) {
            $row['srno'] = ++$srno;
            // Add edit, status, and delete buttons for the record
            $row['option'] = optionButton('edit', route('association.form', ['id' => $row['id']]));
            $row['option'] .= optionButton('status', route('association.status', ['id' => $row['id']]), $row);
            $row['option'] .= optionButton('delete', route('association.delete', ['id' => $row['id']]));

            $row['created_at'] = formatDateWithTimezone($row['created_at']);
        }

        // Return the JSON response for DataTables
        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => $datatableData['totalRecords'],
            'recordsFiltered' => $datatableData['filteredRecords'],
            'data' => $associationData,
        ]);
    }

    public function form(Request $request, int $id = null)
    {
        $data['associationObject'] = $id ? Association::withTrashed()->findOrFail($id) : new Association;
        return view('web.association.form', $data);
    }
    public function formSave(Request $request, int $id = null)
    {
        $rules = [
            'name' => 'required|string|max:255|iunique:associations,name,' . $id,
        ];

        // Validate the user input data
        PublicException::Validator($request->all(), $rules);

        $associationObject = $id ? Association::withTrashed()->findOrFail($id) : new Association;
        $associationObject->name = $request->name;
        $associationObject->location = $request->location;
        $associationObject->description = $request->description;
        $associationObject->parent_id = $request->parent_id;
        $associationObject->association_type = $request->association_type;
        $associationObject->permission_type = $request->permission_type;

        // if data not save show error
        PublicException::NotSave($associationObject->save());

        // return a success response with the association data
        return Helper::SuccessReturn([], 'RECORD_SAVED');
    }
    public function changeStatus(Request $request, int $id)
    {
        $associationObject = Association::withTrashed()->findOrFail($id);
        if ($associationObject->deleted_at === null) {
            $associationObject->delete();
            // return a success response with the association data
            return Helper::SuccessReturn([], 'RECORD_INACTIVE');
        } else {
            $associationObject->restore();
            // return a success response with the association data
            return Helper::SuccessReturn([], 'RECORD_ACTIVE');
        }
    }

    public function deleteRow(Request $request, int $id)
    {
        $associationObject = Association::withTrashed()->findOrFail($id);
        $associationObject->forceDelete();
        return Helper::SuccessReturn([], 'RECORD_DELETE');
    }
}
