<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\PublicException;
use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Translate;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TranslateController extends Controller
{
    public function index(Request $request)
    {
        $data = ['page_title' => 'Translate', 'page_icon' => 'fa-language', 'table_url' => route('translate.data')];

        return view('web.translate.index', $data);
    }


    /**
     * Get data for DataTables.
     */
    public function getData(Request $request)
    {
        // Define the data table structure
        $datatable = [
            'column' => array_merge(['option' => 'Option', 'srno' => 'Sr. No.'], TRANSLATE_LANGUAGE, ['created_at' => 'Create Date']),
            'search_column' => array_merge(['created_at'], array_keys(TRANSLATE_LANGUAGE)),
            'order_column' => array_merge(['created_at'], array_keys(TRANSLATE_LANGUAGE)),
        ];

        // Get the model object with trashed records
        $translateObject = Translate::query();

        // Process the data table filters and options
        $datatableData = Helper::Datatable($datatable, $translateObject);

        // Get the paginated and ordered data
        $translateData = $datatableData['modelObject']->get()->makeVisible('created_at')->toArray();

        $srno = $datatableData['start'];

        // Add options for each record in the DataTable
        foreach ($translateData as &$row) {
            $row['srno'] = ++$srno;

            // Add edit, status, and delete buttons for the record
            $row['option'] = optionButton('edit', route('translate.form', ['id' => $row['id']]));
            $row['option'] .= optionButton('delete', route('translate.delete', ['id' => $row['id']]));

            $row['created_at'] = formatDateWithTimezone($row['created_at']);
        }

        // Return the JSON response for DataTables
        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => $datatableData['totalRecords'],
            'recordsFiltered' => $datatableData['filteredRecords'],
            'data' => $translateData,
        ]);
    }



    public function form(Request $request, int $id = null)
    {
        $data['translateObject'] = $id ? Translate::findOrFail($id) : new Translate;
        return view('web.translate.form', $data);
    }


    public function formSave(Request $request, int $id = null)
    {
        $rules = [
            'en' => 'required|string|max:255|iunique:translations,en,' . $id,
        ];

        foreach (array_keys(TRANSLATE_LANGUAGE_EXCEPT_ENGLISH) as $value) {
            $rules[$value] = 'nullable|string|max:255';
        }

        $message = array_map(function ($value) {
            return strtolower($value) . ' text';
        }, TRANSLATE_LANGUAGE);

        // Validate the user input data
        PublicException::Validator($request->all(), $rules, [], $message);

        $translateObject = $id ? Translate::findOrFail($id) : new Translate;
        $translateObject = Helper::UpdateObjectIfKeyExist($translateObject,  array_keys(TRANSLATE_LANGUAGE));

        // if data not save show error
        PublicException::NotSave($translateObject->save());


        return Helper::SuccessReturn([], 'RECORD_SAVED');
    }


    public function deleteRow(Request $request, int $id)
    {
        $translateObject = Translate::findOrFail($id);
        $translateObject->forceDelete();
        return Helper::SuccessReturn([], 'RECORD_DELETE');
    }
}
