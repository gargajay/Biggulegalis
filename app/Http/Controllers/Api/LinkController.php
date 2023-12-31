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
use App\Models\UserAssociation;
use Carbon\carbon;
use Carbon\Carbon as CarbonCarbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        return Helper::SuccessReturn($links, 'LINK_FETCH');
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
            'association_id' => ['nullable']
        ];
        // Validate the user input data
        PublicException::Validator($request->all(), $rules);
        $links = Invitation::with('association', 'user');


        $officeBearesIds =   UserAssociation::where(function ($query) {
            $query->orWhereJsonContains('roles', 5)
                ->orWhereJsonContains('roles', 6)
                ->orWhereJsonContains('roles', 4)
                ->orWhereJsonContains('roles', 7);
        })->where('status', 1)
            ->where('association_id', $request->association_id)
            ->pluck('user_id')->toArray();








        if (!empty($request->association_id) && in_array(Auth::id(), $officeBearesIds)) {
            $links->where('association_id', $request->association_id)->where('type', 'from_association');
        } else {
            $links->where('user_id', Auth::id())->where('type', 'from_user');
        }

        $links = newPagination($links->latest());
        return Helper::SuccessReturn($links, 'LINK_FETCH');
    }

    public function invitationAcceptReject(Request $request)
    {


        $rules = [
            'id' => ['required', 'integer', 'iexists:invitations,id'],
            'type' => ['required']
        ];
        // Validate the user input data
        PublicException::Validator($request->all(), $rules);
        $invitation = Invitation::with('association', 'user')
            ->where('id', $request->id)->first();

        if($invitation->type !='from_user')
        {
          $userPermissionArray =  User::getAllPermissions(Auth::id(),true);
          if(!in_array(10,$userPermissionArray)){
            return response()->json(['success' => FALSE, 'status' =>400, 'message' => __("message.no_permission")], 200);
          }
        }

        Log::info("invitation" . json_encode($invitation));
        $invitation->status = $request->type;

        if ($request->type == 1) {
            $userAssociation =  UserAssociation::where('association_id', $invitation->association_id)->where('user_id', $invitation->user_id)->first();

            if (empty($userAssociation)) {

                $new = true;
                $userAssociation =  new UserAssociation();
            }

            if ($invitation->type == 'from_user') {
                $userAssociation->association_id = $invitation->association_id;
                $userAssociation->user_id = $invitation->user_id;
                ////dd($userAssociation);
                PublicException::NotSave($userAssociation->save());
                $userAssociation = UserAssociation::find($userAssociation->id);
                $userAssociation->roles = [8];
                $userAssociation =   UserAssociation::handleRoles($userAssociation);
            } else {
                $userAssociation->status = 1;
            }

            PublicException::NotSave($userAssociation->save());



            $msg =  'Invitation_Accepted';
        } else {
            $msg =  'Invitation_Rejected';
        }
        if ($invitation->type == 'from_user') {
            if ($msg == 'Invitation_Accepted') {
                Auth::user()->tokens->each(function ($token, $key) {
                    // Delete the access token
                    $token->delete();
                });
            }
        }


        $invitation->delete();

        return Helper::SuccessReturn($invitation, $msg);
    }
}
