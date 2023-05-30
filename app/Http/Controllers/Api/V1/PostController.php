<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiGlobalFunctions;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use App\Models\PostAttachment;
use App\Models\Tag;
use App\Models\Comment;
use App\Repositories\PostRepository;
use Carbon\Carbon;
use Exception, DB;


class PostController extends Controller
{

    /**
     * createPost
     *
     * @param  mixed $request
     * @return void
     */
    public static function createPost(Request $request)
    {
        $data = [];
        try {
            // dd($request->all());
            $validator = (object) Validator::make($request->all(), [
                'post_type' => 'required',
                'content' => 'nullable',
                'post_visibility' => 'required',
            ]);
            if ($validator->fails()) {
                return ApiGlobalFunctions::sendError('Validation Error.', $validator->messages(), 404);
            }
            $authUser = $request->get('Auth');
            $post = (object) new Post();
            $post->user_id = $authUser->id;
            $post->post_type = $postType = $request->request->get('post_type', false);
            $post->post_visibility = $request->request->get('post_visibility', false);
            $post->description = $request->request->get('content', false);
            $post->category_id = $request->request->get('category_id', false);
            $mediaUrls = $request->request->get('media_url') ?? [];
            $mentionedUsers = $request->request->get('mentioned_users') ?? [];
            $mentionedTags = $request->request->get('mentioned_tags') ?? [];
            
            if ($post->save()) {
                if (!empty($mediaUrls)) {
                    $ordering = 1;
                    $mediaType = 'image';
                    if ($postType == 2) {
                        $mediaType = 'video';
                    } elseif ($postType == 3) {
                        $mediaType = 'document';
                    }
                    foreach ($mediaUrls as $value) {
                        if (!empty($value)) {
                            $attachmentData = [];
                            $attachmentData['post_id'] = $post->id;
                            $attachmentData['user_id'] = $authUser->id;
                            $attachmentData['title'] = '';
                            $attachmentData['name'] = basename($value);
                            $attachmentData['url'] = $value;
                            $attachmentData['type'] = $mediaType;
                            $attachmentData['ordering'] = $ordering;
                            PostAttachment::create($attachmentData);
                            $ordering++;
                        }
                    }
                }
                if (!empty($mentionedUsers)) {
                    $post->mentionedUsers()->attach($mentionedUsers);
                }
                if (!empty($mentionedTags)) {
                    $hashTagsIds = [];
                    foreach ($mentionedTags as $tagValue) {
                        $existingTag = Tag::where('name', $tagValue)->first();
                        if ($existingTag) {
                            $hashTagsIds[] = $existingTag->id;
                        } else {
                            $tagObj = new Tag();
                            $tagObj->name = $tagValue;
                            $tagObj->save();
                            $hashTagsIds[] = $tagObj->id;
                        }
                    }
                    $post->mentionedTags()->attach($hashTagsIds);
                }
            }

            $postResponse = PostRepository::getSingel(request()->merge(['post_id' => $post->id]));
            $data['status'] = true;
            $data['code'] = config('response.HTTP_OK');
            $data['message'] = ApiGlobalFunctions::messageDefault('post_save');
            $data['data'] = $postResponse;
        } catch (Exception $e) {
            $data['status'] = false;
            $data['code'] =  $e->getCode();
            if (config('constants.DEBUG_MODE')) {
                $data['message'] = 'Error: ' . $e->getMessage();
            } else {
                $data['message'] = ApiGlobalFunctions::messageDefault('oops');
            }
        }
        return ApiGlobalFunctions::responseBuilder($data);
    }

    /**
     * createPost
     *
     * @param  mixed $request
     * @return void
     */
    public static function rePost(Request $request)
    {
        $data = [];
        try {
            // dd($request->all());
            $validator = (object) Validator::make($request->all(), [
                'post_id' => 'required',
            ]);
            if ($validator->fails()) {
                return ApiGlobalFunctions::sendError('Validation Error.', $validator->messages(), 404);
            }
            $authUser = $request->get('Auth');
            $postId = $request->request->get('post_id', false);
            $post = Post::where('id', $postId)->first();
            if (!empty($post)) {
                $user = User::where('id', $authUser->id)->first();
                $post->rePostUpdate($user);
                $createRePost = DB::select(DB::raw("CALL createPostReplica($postId, $authUser->id)"));
                $newPostId = $createRePost[0]->new_post_id;
                //Post::where('id', $postId)->increment('repost_count', 1, ['repost_at' => Carbon::now()]);
                $postData = Post::where('id', $postId)->withCount(['rePost'])->first();
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('post_save');
                $data['data'] = [
                    'post_id' => $postData->id,
                    're_post' => true,
                    're_post_count' => $postData->re_post_count,
                    'new_post_id' => $newPostId
                ];
            } else {
                $data['status'] = false;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('record_not_found');
            }
        } catch (Exception $e) {
            $data['status'] = false;
            $data['code'] =  $e->getCode();
            if (config('constants.DEBUG_MODE')) {
                $data['message'] = 'Error: ' . $e->getMessage();
            } else {
                $data['message'] = ApiGlobalFunctions::messageDefault('oops');
            }
        }
        return ApiGlobalFunctions::responseBuilder($data);
    }

    /**
     * getPost
     *
     * @param  mixed $request
     * @return void
     */
    public static function getPost(Request $request)
    {
        try {
            $post = PostRepository::getSingel($request);
            $data['status'] = true;
            $data['code'] = config('response.HTTP_OK');
            $data['message'] = ApiGlobalFunctions::messageDefault('record_found');
            $data['data'] = $post;
        } catch (Exception $e) {
            $data['status'] = false;
            $data['code'] =  $e->getCode();
            if (config('constants.DEBUG_MODE')) {
                $data['message'] = 'Error: ' . $e->getMessage();
            } else {
                $data['message'] = ApiGlobalFunctions::messageDefault('oops');
            }
        }
        return ApiGlobalFunctions::responseBuilder($data);
    }

    /**
     * tagsList
     *
     * @param  mixed $request
     * @return void
     */
    public static function tagsList(Request $request)
    {
        try {
            $tags = Tag::status()->pluck('name')->toArray();
            $data['status'] = true;
            $data['code'] = config('response.HTTP_OK');
            $data['message'] = ApiGlobalFunctions::messageDefault('record_found');
            $data['data'] = $tags;
        } catch (Exception $e) {
            $data['status'] = false;
            $data['code'] =  $e->getCode();
            if (config('constants.DEBUG_MODE')) {
                $data['message'] = 'Error: ' . $e->getMessage();
            } else {
                $data['message'] = ApiGlobalFunctions::messageDefault('oops');
            }
        }
        return ApiGlobalFunctions::responseBuilder($data);
    }

    /**
     * getHashTagTimeline
     *
     * @param  mixed $request
     * @return void
     */
    public static function getHashTagTimeline(Request $request)
    {
        $data = [];
        try {
            // dd($request->all());
            $validator = (object) Validator::make($request->all(), [
                'tag' => 'required',
            ]);
            if ($validator->fails()) {
                return ApiGlobalFunctions::sendError('Validation Error.', $validator->messages(), 404);
            }
            $post = PostRepository::getHashTagTimeline($request);
            $data['status'] = true;
            $data['code'] = config('response.HTTP_OK');
            $data['message'] = ApiGlobalFunctions::messageDefault('record_found');
            $data['data'] = $post;
        } catch (Exception $e) {
            $data['status'] = false;
            $data['code'] =  $e->getCode();
            if (config('constants.DEBUG_MODE')) {
                $data['message'] = 'Error: ' . $e->getMessage();
            } else {
                $data['message'] = ApiGlobalFunctions::messageDefault('oops');
            }
        }
        return ApiGlobalFunctions::responseBuilder($data);
    }

    /**
     * getTimeline
     *
     * @param  mixed $request
     * @return void
     */
    public static function getTimeline(Request $request)
    {
        $data = [];
        try {
            $postData = PostRepository::getAllData($request);
            $data['status'] = true;
            $data['code'] = config('response.HTTP_OK');
            $data['message'] = ApiGlobalFunctions::messageDefault('record_found');
            $data['data'] = $postData;
        } catch (Exception $e) {
            $data['status'] = false;
            $data['code'] =  $e->getCode();
            if (config('constants.DEBUG_MODE')) {
                $data['message'] = 'Error: ' . $e->getMessage();
            } else {
                $data['message'] = ApiGlobalFunctions::messageDefault('oops');
            }
        }
        return ApiGlobalFunctions::responseBuilder($data);
    }
    /**
     * getMyTimeline
     *
     * @param  mixed $request
     * @return void
     */
    public static function getUserData(Request $request)
    {
        $data = $response = [];
        try {
            $user = $request->get('Auth');
            $validator = (object) Validator::make($request->all(), [
                'user_id' => 'nullable',
                'type' => 'required',
            ]);
            if ($validator->fails()) {
                return ApiGlobalFunctions::sendError('Validation Error.', $validator->messages(), 404);
            }
           
            $type = $request->request->get('type', false);
            switch ($type) {
                case 'post':
                    $response = PostRepository::getUserPostTimeline($request);
                    break;
                case 'media':
                    $response = PostRepository::getUserPostMedia($request);
                    break;
                case 'post_reply':
                    $response = PostRepository::getUserPostReply($request);
                    break;
                case 'like':
                    $response = PostRepository::getUserPostLike($request);
                    break;
                default:
                    $response = [];
            }
            if (!empty($response)) {
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('record_found');
                $data['data'] = $response;
            } else {
                $data['status'] = false;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('list_not_found');
            }            
        } catch (Exception $e) {
            $data['status'] = false;
            $data['code'] =  $e->getCode();
            if (config('constants.DEBUG_MODE')) {
                $data['message'] = 'Error: ' . $e->getMessage();
            } else {
                $data['message'] = ApiGlobalFunctions::messageDefault('oops');
            }
        }
        return ApiGlobalFunctions::responseBuilder($data);
    }

    /**
     * viewPostUpdate
     *
     * @param  mixed $request
     * @return void
     */
    public static function viewPostUpdate(Request $request)
    {
        $user = $request->get('Auth');
        try {
            $validator = (object) Validator::make($request->all(), [
                'post_id' => 'required',
            ]);
            if ($validator->fails()) {
                return ApiGlobalFunctions::sendError('Validation Error.', $validator->messages(), 404);
            }
            $postId = $request->request->get('post_id', false);
            $post = Post::where('id', $postId)->first();
            if (!empty($post)) {
                $user = User::where('id', $user->id)->first();
                if (!$post->postView()->where('post_view.user_id', $user->id)->exists()) {
                    $post->postViewUpdate($user);
                    $data['status'] = true;
                    $data['code'] = config('response.HTTP_OK');
                    $data['message'] = ApiGlobalFunctions::messageDefault('view_update');
                } else {
                    $data['status'] = true;
                    $data['code'] = config('response.HTTP_OK');
                    $data['message'] = ApiGlobalFunctions::messageDefault('view_already_update');
                }
                $postData = Post::where('id', $postId)->withCount(['postView'])->first();
                $data['data'] = [
                    'post_id' => $postId,
                    'post_view_count' => $postData->post_view_count,
                    'post_view_status' => true,
                ];
            } else {
                $data['status'] = false;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('record_not_found');
            }
        } catch (\Exception $e) {
            $data['status'] = false;
            $data['code'] =  $e->getCode();
            if (config('constants.DEBUG_MODE')) {
                $data['message'] = 'Error: ' . $e->getMessage();
            } else {
                $data['message'] = ApiGlobalFunctions::messageDefault('oops');
            }
        }
        return ApiGlobalFunctions::responseBuilder($data);
    }

    /**
     * likePostUpdate
     *
     * @param  mixed $request
     * @return void
     */
    public static function likePostUpdate(Request $request)
    {
        $user = $request->get('Auth');
        try {
            $validator = (object) Validator::make($request->all(), [
                'post_id' => 'required',
            ]);
            if ($validator->fails()) {
                return ApiGlobalFunctions::sendError('Validation Error.', $validator->messages(), 404);
            }
            $postId = $request->request->get('post_id', 0);
            $post = Post::where('id', $postId)->first();
            if (!empty($post)) {
                $user = User::where('id', $user->id)->first();
                $postLikeResponse = $post->postLikeUpdate($user);
                $postData = Post::where('id', $postId)->withCount(['postLike'])->first();
                $status = false;
                if (!empty($postLikeResponse['attached'])) {
                    $data['status'] = true;
                    $data['code'] = config('response.HTTP_OK');
                    $data['message'] = ApiGlobalFunctions::messageDefault('like_update');
                    $status = true;
                } else {
                    $data['status'] = true;
                    $data['code'] = config('response.HTTP_OK');
                    $data['message'] = ApiGlobalFunctions::messageDefault('unlike_update');
                }
                $data['data'] = [
                    'post_id' => $postId,
                    'post_like_count' => $postData->post_like_count,
                    'post_like_status' => $status,
                ];
            } else {
                $data['status'] = false;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('record_not_found');
            }
        } catch (\Exception $e) {
            $data['status'] = false;
            $data['code'] =  $e->getCode();
            if (config('constants.DEBUG_MODE')) {
                $data['message'] = 'Error: ' . $e->getMessage();
            } else {
                $data['message'] = ApiGlobalFunctions::messageDefault('oops');
            }
        }
        return ApiGlobalFunctions::responseBuilder($data);
    }

    /**
     * postComment
     *
     * @param  mixed $request
     * @return void
     */
    public static function postComment(Request $request)
    {
        $authUser = $request->get('Auth');
        try {
            $validator = (object) Validator::make($request->all(), [
                'comment' => 'required',
                'post_id' => 'required',
            ]);
            if ($validator->fails()) {
                return ApiGlobalFunctions::sendError('Validation Error.', $validator->messages(), 404);
            }
            $input = $request->all();
            
            $comment = (object) new Comment();
            $comment->user_id = $authUser->id;
            $comment->post_id = $postId = $request->request->get('post_id', 0);
            $comment->post_type = $request->request->get('post_type', 1);
            $comment->parent_id = $request->request->get('parent_id', 0);
            $comment->comment = $request->request->get('comment', false);
            $comment->media_url = $request->request->get('media_url', false);
            
            $mentionedUsers = $request->request->get('mentioned_users') ?? [];
            $mentionedTags = $request->request->get('mentioned_tags') ?? [];
            if ($comment->save()) {
                if (!empty($mentionedUsers)) {
                    $comment->mentionedUsers()->attach($mentionedUsers);
                }
                if (!empty($mentionedTags)) {
                    $hashTagsIds = [];
                    foreach ($mentionedTags as $tagValue) {
                        $existingTag = Tag::where('name', $tagValue)->first();
                        if ($existingTag) {
                            $hashTagsIds[] = $existingTag->id;
                        } else {
                            $tagObj = new Tag();
                            $tagObj->name = $tagValue;
                            $tagObj->save();
                            $hashTagsIds[] = $tagObj->id;
                        }
                    }
                    $comment->mentionedTags()->attach($hashTagsIds);
                }
                $postData = (object) PostRepository::getSingel($request);
                //$postData = Post::where('id', $postId)->withCount(['comments'])->first();
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('add_comment');
                $data['data'] = [
                    'post_comment_count' => $postData->comments_count,
                    'post_comment_status' => true,
                    'post_data' => $postData,

                ];
            } else {
                $data['status'] = false;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('invalid_request');
            }
        } catch (\Exception $e) {
            $data['status'] = false;
            $data['code'] =  $e->getCode();
            if (config('constants.DEBUG_MODE')) {
                $data['message'] = 'Error: ' . $e->getMessage();
            } else {
                $data['message'] = ApiGlobalFunctions::messageDefault('oops');
            }
        }
        return ApiGlobalFunctions::responseBuilder($data);
    }
}
