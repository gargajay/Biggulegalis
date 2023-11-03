<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\PublicException;
use App\Helper\Helper;
use App\Helper\PushNotification;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Gallery;
use App\Models\User;
use Carbon\carbon;
use Carbon\Carbon as CarbonCarbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GalleryController extends Controller
{
    public function getGallery(Request $request)
    {
        

        $rules = [
            'association_id' => ['required', 'integer', 'iexists:associations,id']
        ];
        // Validate the user input data
        PublicException::Validator($request->all(), $rules);
        $gallerys = Gallery::with('association')
            ->where('association_id', $request->association_id);
        $gallerys = newPagination($gallerys->latest());
        return Helper::SuccessReturn($gallerys,'LINK_FETCH');
    }

    public function getGalleryDetails(Request $request)
    {
        $rules = [
            'id' => ['required', 'integer', 'iexists:gallerys,id']
        ];
        // Validate the user input data
        PublicException::Validator($request->all(), $rules);
        $gallerys = Gallery::with('association')->where('id', $request->id)->first();
        return Helper::SuccessReturn($gallerys, 'LINK_DETAILS');
    }

   

    public function gallery(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable'],
            'image' => ['nullable', 'mimes:jpeg,png,jpg,gif'],
            'date' => ['required', 'date_format:Y-m-d'],
            'id' => ['nullable', 'integer', 'iexists:gallery,id'],
            'association_id' => ['required', 'integer', 'iexists:associations,id']

        ];

        // Validate the user input data
        PublicException::Validator($request->all(), $rules);
        $id = $request->id;
        $gallery = !empty($id) ? Gallery::find($request->id) : new Gallery;
        $gallery->user_id = Auth::id();
        $gallery = Helper::UpdateObjectIfKeyNotEmpty($gallery, [
            'name',
            'date',
            'description',
            'association_id',
            'image'
        ]);

        if ($request->has('image')) {
            $gallery->image = Helper::FileUpload('image', EVENT_IMAGE_INFO);
        }

        $gallery->user_id = Auth::id();
       
        // if data not save show error
    
        PublicException::NotSave($gallery->save());
        // add gallery members
      
        return Helper::SuccessReturn($gallery->load('association'), !empty($id) ? 'LINK_UPDATED' : 'LINK_ADDED');
    }

    public function deleteGallery(Request $request)
    {
        $rules = [
            'id' => ['required', 'integer', 'iexists:gallery,id']
        ];

        // Validate the user input data
        PublicException::Validator($request->all(), $rules);
        Gallery::find($request->id)->delete();
        return Helper::SuccessReturn(null, 'LINK_DELETED');
    }
}
