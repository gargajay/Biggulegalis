<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Helper\Helper;
use App\Exceptions\PublicException;

use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $data = ['page_title' => 'Documents', 'page_icon' => 'fa-user', 'table_url' => route('document.data')];

        return view('web.document.index', $data);
    }
    /**
     * Get data for DataTables.
     */
    public function getData(Request $request)
    {
        // Define the data table structure
        $datatable = [
            'column' => ['option' => 'Option', 'srno' => 'S No.', 'title' => 'Name','file' => 'File', 'status' => 'Status'],
            'search_column' => ['title'],
            'order_column' => ['title'],
        ];

        // Process the data table filters and options
        $documentObject = Document::withTrashed();
        // Process the data table filters and options
        $datatableData = Helper::Datatable($datatable, $documentObject);
        // Get the paginated and ordered data
        $documentData = $datatableData['modelObject']->get()->append('status')->makeVisible('created_at')->toArray();
        $srno = $datatableData['start'];

        // Add options for each record in the DataTable
        foreach ($documentData as &$row) {
            $row['srno'] = ++$srno;
            // Add edit, status, and delete buttons for the record
            $row['option'] = optionButton('edit', route('document.form', ['id' => $row['id']]));
            $row['option'] .= optionButton('status', route('document.status', ['id' => $row['id']]), $row);
            $row['option'] .= optionButton('delete', route('document.delete', ['id' => $row['id']]));

            $row['created_at'] = formatDateWithTimezone($row['created_at']);
        }

        // Return the JSON response for DataTables
        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => $datatableData['totalRecords'],
            'recordsFiltered' => $datatableData['filteredRecords'],
            'data' => $documentData,
        ]);
    }

    public function form(Request $request, int $id = null)
    {
        $data['documentObject'] = $id ? Document::withTrashed()->findOrFail($id) : new Document;
        return view('web.document.form', $data);
    }
    public function formSave(Request $request, int $id = null)
    {
        $rules = [
            'name' => 'required|string|max:255|iunique:documents,name,' . $id,
        ];

        // Validate the user input data
        PublicException::Validator($request->all(), $rules);

        $documentObject = $id ? Document::withTrashed()->findOrFail($id) : new Document;
        $documentObject->name = $request->name;

        // if data not save show error
        PublicException::NotSave($documentObject->save());

        // return a success response with the document data
        return Helper::SuccessReturn([], 'RECORD_SAVED');
    }
    public function changeStatus(Request $request, int $id)
    {
        $documentObject = Document::withTrashed()->findOrFail($id);
        if ($documentObject->deleted_at === null) {
            $documentObject->delete();
            // return a success response with the document data
            return Helper::SuccessReturn([], 'RECORD_INACTIVE');
        } else {
            $documentObject->restore();
            // return a success response with the document data
            return Helper::SuccessReturn([], 'RECORD_ACTIVE');
        }
    }

    public function deleteRow(Request $request, int $id)
    {
        $documentObject = Document::withTrashed()->findOrFail($id);
        $documentObject->forceDelete();
        return Helper::SuccessReturn([], 'RECORD_DELETE');
    }
}
