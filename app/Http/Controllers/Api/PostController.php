<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\PublicException;
use App\Helper\Helper;
use App\Helper\PushNotification;
use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostLike;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function getPost(Request $request)
    {
        $user_id = $request->user_id ?? Auth::id();
        $data['user'] = User::where('id', $user_id)->with('goal', 'workoutHours')->first();
        $posts = newPagination(Post::with('images')->where('user_id', $user_id)->latest());
        $data['posts'] = $posts['data'];
        return Helper::SuccessReturnPagination($data, $posts['totalPages'], $posts['nextPageUrl'], 'POST_FETCH');
    }

    public function addUpdatePost(Request $request)
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'images.*' => ['nullable', 'mimes:jpeg,png,jpg,gif'],
            'id' => ['nullable', 'integer', 'iexists:posts,id']
        ];
        // Validate the user input data
        PublicException::Validator($request->all(), $rules);
        $id = $request->id;
        $post = !empty($id) ? Post::find($id) : new Post;
        $post->user_id = Auth::id();
        $post->title = $request->title;
        $post->description = $request->description;
        PublicException::NotSave($post->save());
        $modelName = class_basename($post);
        if ($request->has('images')) {
            $images = Helper::MultiFileUpload('images', POST_IMAGE_INFO);
            if (!empty($images)) {
                foreach ($images as $image) {
                    $imageObject = new Image;
                    $imageObject->model_name = $modelName;
                    $imageObject->model_id = $post->id;
                    $imageObject->file_name = $image['file_name'];
                    $imageObject->file_type = $image['file_type'];
                    $imageObject->file_extension = $image['file_extension'];
                    PublicException::NotSave($imageObject->save());
                }
            }
        }
        return Helper::SuccessReturn($post, !empty($id) ? 'POST_UPDATED' : 'POST_ADDED');
    }

    public function deletePost(Request $request)
    {
        $rules = [
            'id' => ['required', 'integer', 'iexists:posts,id']
        ];

        // Validate the user input data
        PublicException::Validator($request->all(), $rules);
        Post::find($request->id)->delete();
        return Helper::SuccessReturn(null, 'POST_DELETED');
    }

    public function likeDislikePost(Request $request)
    {
        $rules = [
            'post_id' => ['required', 'integer', 'iexists:posts,id']
        ];

        // Validate the user input data
        PublicException::Validator($request->all(), $rules);
        $userDetail = User::find(Auth::id());
        $checkLike = PostLike::where(['user_id' => $userDetail->id, 'post_id' => $request->post_id])->first();
        if (!empty($checkLike)) {
            $checkLike->delete();
            return Helper::SuccessReturn(null, 'POST_DISLIKE');
        } else {
            $like = new PostLike;
            $like->user_id = $userDetail->id;
            $like->post_id = $request->post_id;
            PublicException::NotSave($like->save());
            $postDetail = Post::find($request->post_id);
            // if ($postDetail->user_id != $userDetail->id) {
            //     $notificationData = [[
            //         'receiver_id' => $postDetail->user_id,
            //         'title' => ['Post Liked'],
            //         'body' => ['POST_LIKE_NOTIFY' => ['name' => $userDetail->full_name]],
            //         'type' => 'post_like',
            //         'app_notification_data' => $postDetail,
            //         'model_id' => $postDetail->id,
            //         'model_name' => get_class($postDetail),
            //     ]];
            //     PushNotification::Notification($notificationData, true, false, $userDetail->id);
            // }
            return Helper::SuccessReturn(null, 'POST_LIKE');
        }
    }

    public function postComment(Request $request)
    {
        $rules = [
            'post_id' => ['required', 'integer', 'iexists:posts,id'],
            'comment' => ['required', 'string', 'max:255'],
        ];

        // Validate the user input data
        PublicException::Validator($request->all(), $rules);
        $userDetail = User::find(Auth::id());
        $id = $request->id;
        $comment = !empty($id) ? PostComment::find($id) : new PostComment;
        $comment->user_id = $userDetail->id;
        $comment->post_id = $request->post_id;
        $comment->comment = $request->comment;
        PublicException::NotSave($comment->save());
        $postDetail = Post::find($request->post_id);
        // if ($postDetail->user_id != $userDetail->id && empty($id)) {
        //     $notificationData = [[
        //         'receiver_id' => $postDetail->user_id,
        //         'title' => ['Post Comment'],
        //         'body' => ['POST_COMMENT_NOTIFY' => ['name' => $userDetail->full_name]],
        //         'type' => 'post_comment',
        //         'app_notification_data' => $postDetail,
        //         'model_id' => $postDetail->id,
        //         'model_name' => get_class($postDetail),
        //     ]];
        //     PushNotification::Notification($notificationData, true, false, $userDetail->id);
        // }
        return Helper::SuccessReturn($comment->load('user'), !empty($id) ? 'COMMENT_UPDATED' : 'COMMENT_ADDED');
    }

    public function deletePostComment(Request $request)
    {
        $rules = [
            'id' => ['required', 'integer', 'iexists:post_comments,id']
        ];

        // Validate the user input data
        PublicException::Validator($request->all(), $rules);
        PostComment::find($request->id)->delete();
        return Helper::SuccessReturn(null, 'COMMENT_DELETED');
    }

    public function deletePostImage(Request $request)
    {
        $rules = [
            'id' => ['required', 'integer', 'iexists:images,id']
        ];

        // Validate the user input data
        PublicException::Validator($request->all(), $rules);
        Image::find($request->id)->delete();
        return Helper::SuccessReturn(null, 'IMAGE_DELETED');
    }


    public function getpostDetail(Request $request)
    {
        $rules = [
            'post_id' => ['required', 'integer', 'iexists:posts,id']
        ];

        // Validate the user input data
        PublicException::Validator($request->all(), $rules);

        $post = Post::with('likes.user', 'comments.user', 'images')
            ->where('user_id', Auth::id())
            ->find($request->post_id);

        return Helper::SuccessReturn($post, 'POST_FETCH');
    }

    public function getComment(Request $request)
    {
        $rules = [
            'post_id' => ['required', 'integer', 'exists:posts,id'],
        ];

        // Validate the user input data
        PublicException::Validator($request->all(), $rules);

        $comments = newPagination(PostComment::with('user')->where('post_id', $request->post_id)->latest());
        return Helper::SuccessReturnPagination($comments['data'], $comments['totalPages'], $comments['nextPageUrl'], 'POST_COMMENTS_FETCH');
    }
}
