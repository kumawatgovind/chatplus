<?php

namespace App\Repositories;

use App\Models\ContactSync;
use App\Models\Friend;
use App\Models\Status;
use App\Models\StatusMedia;
use App\Models\StatusView;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function is;
use function is_null;
use Exception;

class UserStatusRepository
{

    /**
     * create
     *
     * @param  mixed $request
     * @return obj
     */
    public static function create(Request $request)
    {
        DB::beginTransaction();
        try {
            $authUser = $request->get('Auth');
            $userStatus = Status::where('statuses.user_id', $authUser->id)->first();
            if (empty($userStatus)) {
                $userStatus = new Status();
            }
            $userStatus->user_id = $authUser->id;
            $userStatus->media_type = $statusType = $request->input('status_type', false);
            // $userStatus->content = $request->input('content', false);
            // $userStatus->start_status = $current->toDateTimeString();
            // $userStatus->end_status = $current->addDays(1)->toDateTimeString();
            $statusMedia = $request->input('status_media') ?? [];
            if ($userStatus->save()) {
                if (!empty($statusMedia) && $statusType != 'text') {
                    foreach ($statusMedia as $value) {
                        if (!empty($value)) {
                            $current = Carbon::now();
                            $attachmentData = [];
                            $attachmentData['status_id'] = $userStatus->id;
                            $attachmentData['media_type'] = $statusType;
                            $attachmentData['name'] = $value;
                            $attachmentData['start_status'] = $current->toDateTimeString();
                            $attachmentData['end_status'] = $current->addDays(1)->toDateTimeString();
                            StatusMedia::create($attachmentData);
                        }
                    }
                }
                DB::commit();
                return [
                    'status' => true,
                    'data' => $userStatus
                ];
            } else {
                DB::rollBack();
                return [
                    'status' => false,
                    'data' => []
                ];;
            }
        } catch (Exception $e) {
            DB::rollBack();
            return [
                'status' => false,
                'data' => $e->getMessage()
            ];
        }
    }


    /**
     * myStories
     *
     * @param  mixed $request
     * @return void
     */
    public static function myStories(Request $request)
    {
        $authUser = $request->get('Auth');
        $current = Carbon::now();
        $query = Status::where('statuses.user_id', $authUser->id)
            ->with([
                'user',
                'statusMedias' => function ($q) use($current) {
                    return $q->where('start_status', '<=', $current)
                    ->where('end_status', '>=', $current)
                    ->orderBy('status_medias.id', 'asc')->withCount('statusView');
                },
                'statusMedias.statusView' => function ($q) use($authUser) {
                    return $q->where('status_views.view_user_id', $authUser->id);
                }
            ]);
        $userStatus = $query->orderBy('statuses.id', 'desc')->get();
        // dd($userStatus);
        $statusOutput = [];
        if ($userStatus->count() > 0) {
            foreach ($userStatus as $status) {
                $userStoryList = [];
                if ($status->statusMedias->count() > 0) {
                    foreach ($status->statusMedias as $storyList) {
                        $userStoryList[] = [
                            'status_media_id' => $storyList->id,
                            'media_type' => $storyList->media_type,
                            'media_url' => $storyList->base_path.'/'.$storyList->name,
                            'name' => $status->user->name,
                            'start_time' => strtotime($storyList->start_status),
                            'end_time' => strtotime($storyList->end_status),
                            'startTime' => date('d-m-Y h:i A', strtotime($storyList->start_status)),
                            'endTime' => date('d-m-Y h:i A', strtotime($storyList->end_status)),
                            'is_seen' => ($storyList->owner_view) ? true : false,
                            'total_view' => ($storyList->statusView->count() > 0) ? $storyList->status_view_count-1 : $storyList->status_view_count,
                        ];
                    }
                }
                if (!empty($userStoryList)) {
                    $statusOutput[] = [
                        'id' => $status->id,
                        'user_name' => $status->user->name,
                        'phone_number' => $status->user->phone_number,
                        'userStoryList' => $userStoryList
                    ];
                }
            }
        }
        return $statusOutput;
    }

    /**
     * friendsStories
     *
     * @param  mixed $request
     * @return void
     */
    public static function friendsStories(Request $request)
    {
        $authUser = $request->get('Auth');
        $myUsers = Friend::select('friend_id')
        ->where('user_id', $authUser->id)
        ->distinct()->get();
        $meFriends = Friend::select('user_id')
        ->where('friend_id', $authUser->id)
        ->distinct()->get();
        // dd($myUsers, $meFriends);
        $myFriendsIds = $myUsers->pluck('friend_id')->toArray();
        $myFriendsUserIds = $meFriends->pluck('user_id')->toArray();
        // dd($myFriendsUserIds, $myFriendsIds);
        $resultFriends = array_merge($myFriendsUserIds, $myFriendsIds);
        $resultFriendsIds = array_unique($resultFriends);
        // dd($resultFriendsIds);
        $current = Carbon::now();
        $query = Status::whereIn('statuses.user_id', $resultFriendsIds)
            ->with([
                'user',
                'statusMedias' => function ($q) use($current,$authUser) {
                    return $q->where('start_status', '<=', $current)
                    ->where('end_status', '>=', $current)
                    ->orderBy('status_medias.id', 'asc')
                    ->withCount('statusView');
                },
                'statusMedias.statusView' => function ($q) use($authUser) {
                    return $q->where('status_views.view_user_id', $authUser->id);
                }
            ]);
        $userStatus = $query->orderBy('statuses.id', 'desc')->get();
        
        $statusOutput = [];
        if ($userStatus->count() > 0) {
            foreach ($userStatus as $status) {
                $userStoryList = [];
                if ($status->statusMedias->count() > 0) {
                    foreach ($status->statusMedias as $storyList) {
                        $userStoryList[] = [
                            'status_media_id' => $storyList->id,
                            'media_type' => $storyList->media_type,
                            'media_url' => $storyList->base_path.'/'.$storyList->name,
                            'name' => $status->user->name,
                            'start_time' => strtotime($storyList->start_status),
                            'end_time' => strtotime($storyList->end_status),
                            'startTime' => date('d-m-Y h:i A', strtotime($storyList->start_status)),
                            'endTime' => date('d-m-Y h:i A', strtotime($storyList->end_status)),
                            'is_seen' => ($storyList->statusView->count() > 0) ? true : false,
                            'total_view' => $storyList->status_view_count,
                        ];
                    }
                }
                if (!empty($userStoryList)) {
                    $statusOutput[] = [
                        'id' => $status->id,
                        'user_name' => $status->user->name,
                        'phone_number' => $status->user->phone_number,
                        'userStoryList' => $userStoryList
                    ];
                }
            }
        }
        return $statusOutput;
    }

    /**
     * deleteProduct
     *
     * @param  mixed $request
     * @return void
     */
    public static function deleteStatus(Request $request)
    {
        DB::beginTransaction();
        try {
            $authUser = $request->get('Auth');
            if ($request->input('status_id', false) > 0) {
                $statusId = $request->input('status_id', false);
                $statusMediaId = $request->input('status_media_id', false);
                if (Status::where(['id' => $statusId, 'user_id' => $authUser->id])->exists()) {
                    StatusMedia::where([
                        'id' => $statusMediaId,
                        'status_id' => $statusId,
                    ])->delete();
                    $statusMediaCount = StatusMedia::where(['status_id' => $statusId])->count();
                    if ($statusMediaCount == 0) {
                        Status::where([
                            'id' => $statusId,
                            'user_id' => $authUser->id
                        ])->delete(); 
                    }
                }
                DB::commit();
                return true;
            } else {
                DB::rollBack();
                return false;
            }
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * statusViewUpdate
     *
     * @param  mixed $request
     * @return obj
     */
    public static function statusViewUpdate(Request $request)
    {
        try {
            $authUser = $request->get('Auth');
            
            // $statusView = new StatusView();
            // $statusView->status_id = $request->input('status_id', false);
            // $statusView->status_media_id = $request->input('status_media_id', false);
            // $statusView->view_user_id = $authUser->id;

            $statusId = $request->input('status_id', false);
            $statusMediaId = $request->input('status_media_id', false);
            $status = Status::where(['id' => $statusId])->first();
            $statusMedia = StatusMedia::where(['id' => $statusMediaId, 'status_id' => $statusId])->first();
            if ($status && $statusMedia) {
                if ($status->user_id == $authUser->id) {
                    StatusMedia::where('id', $statusMediaId)
                    ->update([
                        'owner_view' => 1
                    ]);
                } else {
                    StatusView::updateOrCreate(
                        [
                            'status_media_id' => $statusMediaId,
                            'view_user_id' => $authUser->id
                        ],
                        [
                            'status_id' => $statusId,
                            'status_media_id' => $statusMediaId,
                            'view_user_id' => $authUser->id
                        ]
                    );
                }
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            // dd($e->getMessage());
            return false;
        }
    }
    
    /**
     * statusViewList
     *
     * @param  mixed $request
     * @return obj
     */
    public static function statusViewList(Request $request)
    {
        $authUser = $request->get('Auth');
        $statusId = $request->input('status_id', false);
        $statusMediaId = $request->input('status_media_id', false);
        $query = StatusView::where([
            'status_views.status_id' => $statusId,
            'status_views.status_media_id' => $statusMediaId
        ])
        ->with([
            'viewer' => function ($q) {
                return $q->select('id', 'name', 'profile_image');
            },
        ]);
        $userStatus = $query->orderBy('status_views.id', 'desc')->get();     
        return $userStatus;
    }
}
