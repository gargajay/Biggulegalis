<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\PublicException;
use App\Helper\Helper;
use App\Helper\PushNotification;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Invitation;
use App\Models\Link;
use App\Models\User;
use Carbon\carbon;
use Carbon\Carbon as CarbonCarbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LinkController extends Controller
{
    public function getLink(Request $request)
    {
        

        $rules = [
            'association_id' => ['required', 'integer', 'iexists:associations,id']
        ];
        // Validate the user input data
        PublicException::Validator($request->all(), $rules);
        $links = Link::with('association')
            ->where('association_id', $request->association_id);
        $links = newPagination($links->latest());
        return Helper::SuccessReturn($links,'LINK_FETCH');
    }

    public function getLinkDetails(Request $request)
    {
        $rules = [
            'id' => ['required', 'integer', 'iexists:links,id']
        ];
        // Validate the user input data
        PublicException::Validator($request->all(), $rules);
        $links = Link::with('association')->where('id', $request->id)->first();
        return Helper::SuccessReturn($links, 'LINK_DETAILS');
    }

   

    public function link(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable'],
            'url' => ['required'],
            'id' => ['nullable', 'integer', 'iexists:links,id'],
            'association_id' => ['required', 'integer', 'iexists:associations,id']

        ];

        // Validate the user input data
        PublicException::Validator($request->all(), $rules);
        $id = $request->id;
        $link = !empty($id) ? Link::find($request->id) : new Link;
        $link->user_id = Auth::id();
        $link = Helper::UpdateObjectIfKeyNotEmpty($link, [
            'name',
            'url',
            'description',
            'association_id'
        ]);

        $link->user_id = Auth::id();
       
        // if data not save show error
    
        PublicException::NotSave($link->save());
        // add link members
      
        return Helper::SuccessReturn($link->load('association'), !empty($id) ? 'LINK_UPDATED' : 'LINK_ADDED');
    }

    public function deleteLink(Request $request)
    {
        $rules = [
            'id' => ['required', 'integer', 'iexists:links,id']
        ];

        // Validate the user input data
        PublicException::Validator($request->all(), $rules);
        Link::find($request->id)->delete();
        return Helper::SuccessReturn(null, 'LINK_DELETED');
    }

    public function getInviationList(Request $request)
    {
        

        $rules = [
            'association_id' => ['required', 'integer', 'iexists:associations,id']
        ];
        // Validate the user input data
        PublicException::Validator($request->all(), $rules);
        $links = Invitation::with('association','user')
            ->where('association_id', $request->association_id);
        $links = newPagination($links->latest());
        return Helper::SuccessReturn($links,'LINK_FETCH');
    }

    public function invitationAcceptReject(Request $request)
    {
        

        $rules = [
            'id' => ['required', 'integer', 'iexists:invitations,id'],
            'type'=>['required']
        ];
        // Validate the user input data
        PublicException::Validator($request->all(), $rules);
        $invitation = Invitation::with('association','user')
            ->where('association_id', $request->id)->first();
        $invitation->status = $request->type;

        if($request->type == 1){
          $msg =  'Invitation_Accepted';
        }else{
            $msg =  'Invitation_Rejected';

        }
        PublicException::NotSave($invitation->save());
         return Helper::SuccessReturn($invitation,$msg);
    }




    
}
