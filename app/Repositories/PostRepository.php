<?php
namespace App\Repositories;


use App\Models\Post;
use App\Models\PostAttachment;
use App\Models\User;
use Illuminate\Http\Request;
use function is;
use function is_null;

class PostRepository
{    
    /**
     * getAllData
     *
     * @param  mixed $request
     * @return void
     */
    public static function getAllData(Request $request)
    {
        if ($request->request->get('limit', false)) {
            $limit  = $request->request->get('limit', 0);
        } else {
            $limit = config('get.FRONT_END_PAGE_LIMIT');
        }
        $query = new Post;
        $query = (object) self::relatedObj($query);
        $responses = $query->orderBy('created_at', 'desc')->paginate($limit);
        if ($responses->count() > 0) {
            $responses = self::formatData(["responses" => $responses]);
        }
        return $responses;
    }
        
    /**
     * formatData
     *
     * @param  mixed $parms
     * @return void
     */
    public static function formatData(array $parms)
    {
        extract($parms);
        foreach ($responses as $response) {
            if ($response->attachments->count() > 0) {
                foreach ($response->attachments as $aKey => $attachment) {
                    $response->attachments[$aKey] = $attachment->url;
                }
            }
        }
        return $responses;
    }
    /**
     * getSingel
     *
     * @param  mixed $request
     * @return void
     */
    public static function getSingel(Request $request)
    {
        $postId = $request->request->get('post_id', false);
        $query = new Post;
        $query = (object) self::relatedObj($query);
        $query = $query->where('id', $postId);
        $response = $query->first();
        if ($response->attachments->count() > 0) {
            foreach ($response->attachments as $aKey => $attachment) {
                $response->attachments[$aKey] = $attachment->url;
            }
        }
        return $response;
    }
    
    /**
     * relatedObj
     *
     * @param  mixed $obj
     * @return void
     */
    public static function relatedObj($obj):object
    {
        
        return $obj->withCount(['postView', 'comments', 'postLike', 'rePost'])
            ->with([
                'user' => function($q) {
                    $q->select('users.id','name', 'username', 'profile_image', 'users.created_at', 'users.updated_at');
                },
                'attachments' => function($q) {
                    $q->select('post_id', 'user_id', 'url');
                }, 'category' => function($q) {
                    $q->select('id', 'title');
                },'mentionedUsers' => function($q) {
                    $q->select('users.id','name', 'username', 'profile_image', 'users.created_at', 'users.updated_at');
                },
                'mentionedTags',
                'comments' => function($q) {
                    $q->select('id','user_id', 'post_id', 'comment', 'media_url', 'created_at')
                    ->with([
                        'user' => function($q) {
                        $q->select('users.id','name');
                    },'mentionedUsers' => function($q) {
                        $q->select('users.id','name', 'username', 'profile_image', 'users.created_at', 'users.updated_at');
                    },
                    'mentionedTags',
                    ])
                    ->orderBy('created_at', 'desc')
                    ->limit(1);
                }
            ]);
    }
    /*
        'originalPost' => function($q) {
                    $q->with([
                        'user' => function($q) {
                            $q->select('users.id','name', 'username', 'profile_image', 'users.created_at', 'users.updated_at');
                        },
                        'attachments' => function($q) {
                            $q->select('post_id','url');
                        }, 'category' => function($q) {
                            $q->select('id', 'title');
                        },'mentionedUsers' => function($q) {
                            $q->select('users.id','name', 'username', 'profile_image', 'users.created_at', 'users.updated_at');
                        },
                        'mentionedTags',
                        'comments' => function($q) {
                            $q->select('id','user_id', 'post_id', 'comment', 'created_at')
                            ->with(['user' => function($q) {
                                $q->select('users.id','name');
                            }])
                            ->orderBy('created_at', 'desc')
                            ->limit(1);
                        },
                    ]);
            }
    */
    /**
     * getHashTagTimeline
     *
     * @param  mixed $request
     * @return void
     */
    public static function getHashTagTimeline(Request $request)
    {
        if ($request->request->get('limit', false)) {
            $limit  = $request->request->get('limit', 0);
        } else {
            $limit = config('get.FRONT_END_PAGE_LIMIT');
        }
        $query = new Post;
        $query = (object) self::relatedObj($query);
        if ($request->request->get('tag', false)) {
            $tag = $request->request->get('tag', false);
            $query = $query->whereHas('mentionedTags', function($q) use($tag){
                $q->where('name', $tag);
            });
        }
        $responses = $query->orderBy('created_at', 'desc')->paginate($limit);
        if ($responses->count() > 0) {
            $responses = self::formatData(["responses" => $responses]);
        }
        return $responses;
    }

    /**
     * getUserPostTimeline
     *
     * @param  mixed $request
     * @return void
     */
    public static function getUserPostTimeline(Request $request)
    {
        $authUser = $request->get('Auth');
        if ($request->request->get('limit', false)) {
            $limit  = $request->request->get('limit', 0);
        } else {
            $limit = config('get.FRONT_END_PAGE_LIMIT');
        }
        $userId = $request->request->get('user_id', 0);
        if ($userId > 0) {
            $userId = $request->request->get('user_id', 0);
        } else {
            $userId = $authUser->id;
        }
        $query = new Post;
        $query = (object) self::relatedObj($query);
        $query->where('user_id', $userId);
        $responses = $query->orderBy('created_at', 'desc')->paginate($limit);
        if ($responses->count() > 0) {
            $responses = self::formatData(["responses" => $responses]);
        }
        return $responses;
    }

    /**
     * getUserPostMedia
     *
     * @param  mixed $request
     * @return void
     */
    public static function getUserPostMedia(Request $request)
    {
        $authUser = $request->get('Auth');
        if ($request->request->get('limit', false)) {
            $limit  = $request->request->get('limit', 0);
        } else {
            $limit = config('get.FRONT_END_PAGE_LIMIT');
        }
        $userId = $request->request->get('user_id', 0);
        if ($userId > 0) {
            $userId = $request->request->get('user_id', 0);
        } else {
            $userId = $authUser->id;
        }        
        $query = new Post;
        $query = (object) self::relatedObj($query);
        $query->whereIn('post_type', [1,2])->where('user_id', $userId);;
        $responses = $query->orderBy('created_at', 'desc')->paginate($limit);
        if ($responses->count() > 0) {
            $responses = self::formatData(["responses" => $responses]);
        }
        return $responses;
    }

    /**
     * getUserPostReply
     *
     * @param  mixed $request
     * @return void
     */
    public static function getUserPostReply(Request $request)
    {
        $authUser = $request->get('Auth');
        if ($request->request->get('limit', false)) {
            $limit  = $request->request->get('limit', 0);
        } else {
            $limit = config('get.FRONT_END_PAGE_LIMIT');
        }
        $userId = $request->request->get('user_id', 0);
        if ($userId > 0) {
            $userId = $request->request->get('user_id', 0);
        } else {
            $userId = $authUser->id;
        }
        
        $query = new Post;
        $query = (object) self::relatedObj($query);
        $query->whereHas('comments', function($q) use($userId) {
            $q->where('user_id', $userId);
        });
        $responses = $query->orderBy('created_at', 'desc')->paginate($limit);
        if ($responses->count() > 0) {
            $responses = self::formatData(["responses" => $responses]);
        }
        return $responses;
    }

    /**
     * getUserPostLike
     *
     * @param  mixed $request
     * @return void
     */
    public static function getUserPostLike(Request $request)
    {
        $authUser = $request->get('Auth');
        if ($request->request->get('limit', false)) {
            $limit  = $request->request->get('limit', 0);
        } else {
            $limit = config('get.FRONT_END_PAGE_LIMIT');
        }
        $userId = $request->request->get('user_id', 0);
        if ($userId > 0) {
            $userId = $request->request->get('user_id', 0);
        } else {
            $userId = $authUser->id;
        }
        $query = new Post;
        $query->where('user_id', $userId);
        $query = (object) self::relatedObj($query);
        $query->whereHas('postLike', function($q) use($userId) {
            $q->where('user_id', $userId);
        });
        $responses = $query->orderBy('created_at', 'desc')->paginate($limit);
        if ($responses->count() > 0) {
            $responses = self::formatData(["responses" => $responses]);
        }
        return $responses;
    }
}