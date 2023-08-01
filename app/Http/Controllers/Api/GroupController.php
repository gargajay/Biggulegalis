<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\PublicException;
use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\ChatGroup;
use App\Models\User;
use App\Models\UserChatGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    public function chatGroup(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'mimes:jpg,png,jpeg,gif', 'max:2048'],
            'id' => ['nullable', 'integer', 'iexists:chat_groups,id'],

        ];
        // validate input data using the Validator method of the PublicException class
        PublicException::Validator($request->all(), $rules);
        $id = $request->id;
        $groupObj = !empty($id) ? ChatGroup::find($id) : new ChatGroup;
        $groupObj->user_id = Auth::id();
        $groupObj->name = $request->name;
        $groupObj->description = $request->description;
        if ($request->has('image')) {
            $groupObj->image = Helper::FileUpload('image', GROUP_IMAGE_INFO);
        }
        PublicException::NotSave($groupObj->save());
        if (empty($id)) {
            // add admin as memeber
            $userGroupObj = new UserChatGroup;
            $userGroupObj->chat_group_id = $groupObj->id;
            $userGroupObj->user_id = Auth::id();
            $userGroupObj->is_admin = 1;
            PublicException::NotSave($userGroupObj->save());

            //inilitize chat
            $chatObj = new Chat;
            $chatObj->sender_id = Auth::id();
            $chatObj->receiver_id = $groupObj->id;
            $chatObj->model = get_class($groupObj);
            $chatObj->model_id = $groupObj->id;
            $chatObj->type = 2;
            PublicException::NotSave($chatObj->save());
        }
        return Helper::SuccessReturn($groupObj, !empty($id) ? 'GROUP_UPDATED' : 'GROUP_CREATED');
    }

    public function addMembersToChatGroup(Request $request)
    {
        $rules = [
            'group_id' => ['required', 'integer', 'iexists:chat_groups,id'],
            'members' => ['required'],

        ];
        // validate input data using the Validator method of the PublicException class
        PublicException::Validator($request->all(), $rules);
        $members = json_decode($request->members);
        foreach ($members as $member) {
            $checkUser = User::find($member);
            if (!empty($checkUser)) {
                $checkMember = UserChatGroup::where(['chat_group_id' => $request->group_id, 'user_id' => $member])->first();
                if (empty($checkMember)) {
                    $userGroupObj = new UserChatGroup;
                    $userGroupObj->chat_group_id = $request->group_id;
                    $userGroupObj->user_id = $member;
                    $userGroupObj->is_admin = 0;
                    PublicException::NotSave($userGroupObj->save());
                }
                $checkChat = Chat::where(['sender_id' => $member, 'receiver_id' => $request->group_id, 'type' => 2])->first();
                if (empty($checkChat)) {
                    //inilitize chat
                    $chatObj = new Chat;
                    $chatObj->sender_id = $member;
                    $chatObj->receiver_id = $request->group_id;
                    $chatObj->model = get_class(new ChatGroup);
                    $chatObj->model_id = $request->group_id;
                    $chatObj->type = 2;
                    PublicException::NotSave($chatObj->save());
                }
            }
        }
        return Helper::SuccessReturn(null, 'MEMBER_ADDED');
    }

    public function removeMemberFromChatGroup(Request $request)
    {
        $rules = [
            'group_id' => ['required', 'integer', 'iexists:chat_groups,id'],
            'member_id' => ['required', 'integer', 'iexists:user_chat_groups,user_id,chat_group_id,' . $request->group_id],
        ];
        PublicException::Validator($request->all(), $rules);
        $adminCheck = UserChatGroup::where(['user_id' => Auth::id(), 'chat_group_id' => $request->group_id, 'is_admin' => 1])->first();
        if (!empty($adminCheck)) {
            UserChatGroup::where(['chat_group_id' => $request->group_id, 'user_id' => $request->member_id])->delete();
            return Helper::SuccessReturn(null, 'MEMBER_DELETED');
        }
        return PublicException::Error('NO_PERMISSION');
    }

    public function makeMemberGroupAdmin(Request $request)
    {
        $rules = [
            'group_id' => ['required', 'iexists:chat_groups,id'],
            'member_id' => ['required', 'iexists:user_chat_groups,user_id,chat_group_id,' . $request->group_id],
        ];

        PublicException::Validator($request->all(), $rules);

        $user_chat_group = UserChatGroup::where('chat_group_id', $request->group_id)->where('user_id', $request->member_id)->first();
        $user_chat_group->is_admin = 1;
        $user_chat_group->save();

        return Helper::SuccessReturn([], 'MEMBER_PROMOTED_TO_ADMIN');
    }

    public function dismissMemberasAdmin(Request $request)
    {
        $rules = [
            'group_id' => ['required', 'iexists:chat_groups,id'],
            'member_id' => ['required', 'iexists:user_chat_groups,user_id,chat_group_id,' . $request->group_id],
        ];

        PublicException::Validator($request->all(), $rules);

        $user_chat_group = UserChatGroup::where('chat_group_id', $request->group_id)->where('user_id', $request->member_id)->first();
        $user_chat_group->is_admin = 0;
        $user_chat_group->save();

        return Helper::SuccessReturn([], 'MEMBER_DISMISS_FROM_ADMIN');
    }

    public function deleteChatGroup(Request $request)
    {
        $rules = [
            'id' => ['required', 'integer', 'iexists:chat_groups,id'],

        ];
        // validate input data using the Validator method of the PublicException class
        PublicException::Validator($request->all(), $rules);
        ChatGroup::find($request->id)->delete();
        return Helper::SuccessReturn(null, 'GROUP_DELETED');
    }
}
