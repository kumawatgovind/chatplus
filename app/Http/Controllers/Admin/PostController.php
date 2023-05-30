<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\PostRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use App\Models\Category;
use App\Models\PostAttachment;
use App\Models\Tag;
use DB;

class PostController extends Controller
{
    /**
     *  Image upload path.
     *
     * @var string
     */
    protected $image_upload_path;

    /** 
     * Storage Class Object.
     *
     * @var \Illuminate\Support\Facades\Storage
     */
    protected $storage;
    
    /**
     * s3Url
     *
     * @var mixed
     */
    protected $s3Url;
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->image_upload_path = 'post';
        $this->storage = Storage::disk('s3');
        $this->s3Url = 'https://s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/' . env('AWS_BUCKET') . '/';
    }
        
    /**
     * index
     *
     * @param  mixed $request
     * @return void
     */
    public function index(Request $request)
    {
        $users = User::status()->pluck('name', 'id')->toArray();
        $posts = Post::sortable(['created_at' => 'desc'])
        ->with('user')->withCount('attachments')
        ->userFilter($request->query('u'))
        ->paginate(config('get.ADMIN_PAGE_LIMIT'));
        return view('Admin.posts.index', compact('posts', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if (!empty($request->query('u'))) {
            $userId = $request->query('u');
            $categories = Category::with('childrenCategory')->get();
            $catData = [];
            foreach ($categories as $category) {
                $catData[$category->id] = $category->title;
            }
            $mentionedUsers = User::status()->where('id', '!=', $userId)->pluck('name', 'id')->toArray();
            $mentionedTags = Tag::status()->pluck('name', 'id')->toArray();
            return view('Admin.posts.createOrUpdate', compact('catData','mentionedUsers', 'mentionedTags'));
        }
        return redirect()->route('admin.users.index')->with('error', 'Please select user profile for create post');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request)
    {
        try {
            $requestData = $request->all();
            $attachments = '';
            $mentionedUsers = $request->request->get('mentioned_users')??[];
            $mentionedTags = $request->request->get('mentioned_tags')??[];
            $requestData['post_type'] = $request->request->get('type', 0);
            unset($requestData['type']);
            unset($requestData['mentioned_users']);
            unset($requestData['mentioned_tags']);
            if (isset($requestData['attachments']) || !empty(isset($requestData['attachments']))) {
                $attachments = $requestData['attachments'];
                unset($requestData['attachments']);
            }
            $requestData['status'] = (isset($requestData['status'])) ? 1 : 0;
            
            $post = Post::create($requestData);
            
            if (!empty($mentionedUsers)) {
                $post->mentionedUsers()->attach($mentionedUsers);
            }
            if (!empty($mentionedTags)) {
                $post->mentionedTags()->attach($mentionedTags);
            }
            if (!empty($attachments)) {
                $ordering = 1;
                foreach ($attachments as $value) {
                    if (!empty($value['name']) || !empty($value['image_path'])) {
                        $attachmentData = [];
                        $attachmentData['post_id'] = $post->id;
                        $attachmentData['title'] = $value['title']??'';
                        $attachmentData['name'] = $value['name'];
                        $attachmentData['url'] = $value['image_path'];
                        $attachmentData['ordering'] = $ordering;
                        PostAttachment::create($attachmentData);
                        $ordering++;
                    }
                }
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError($e->getMessage())->withInput();
        }
        return redirect()->route('admin.users.index')->with('success', 'Post has been saved successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return view('Admin.posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        return view('Admin.posts.createOrUpdate', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(PostRequest $request, Post $post)
    {
        try {
            $requestData = $request->all();
            if ($request->has('attachments')) {
                $delete_previous = PostAttachment::where('post_id', $post->id)->get();
                foreach ($delete_previous as $key => $del) {
                    PostAttachment::where('id', $del->id)->delete();
                    // if (!empty($del->image_name)) {
                    //     unlink(public_path($path . $del->image_name));
                    // }
                }
                $attachments = $request->get('attachments');
                foreach ($attachments as $key => $value) {
                    if (!empty($value['image_name']) || !empty($value['image_path'])) {
                        $attachmentData = [];
                        $attachmentData['post_id'] = $post->id;
                        $attachmentData['title'] = $value['title']??'';
                        $attachmentData['name'] = $value['name'];
                        $attachmentData['url'] = $value['image_path'];
                        $attachmentData['ordering'] = $key;
                        PostAttachment::create($attachmentData);
                    }
                }
            }
            unset($requestData['attachments']);
            $requestData['status'] = (isset($requestData['status'])) ? 1 : 0;
            $post->fill($requestData);
            $post->save();
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError($e->getMessage())->withInput();
        }
        return redirect()->route('admin.posts.index')->with('success', 'Post has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $responce = [];
        DB::beginTransaction();
        try {
            $attachments = PostAttachment::where('id', $post->id)->get();
            foreach ($attachments as $key => $value) {
                if (Storage::disk('s3')->exists($value->image_name)) {
                    Storage::disk('s3')->delete($value->image_name);
                }
                $value->delete();
            }
            $post->delete();
            DB::commit();
            $responce = ['status' => true, 'message' => 'This post has been deleted successfully.', 'data' => []];
        } catch (\Exception $e) {
            DB::rollBack();
            $responce = ['status' => false, 'message' => $e->getMessage()];
        }
        return $responce;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteAttachment($id)
    {
        
        DB::beginTransaction();
        try {
            $postAttachment = PostAttachment::where('id', $id)->first();
            if (Storage::disk('s3')->exists($postAttachment->image_name)) {
                Storage::disk('s3')->delete($postAttachment->image_name);
            }
            $postAttachment->delete();
            DB::commit();
            $responce = ['status' => true, 'message' => 'This post attachment has been deleted successfully.', 'data' => []];
        } catch (\Exception $e) {
            DB::rollBack();
            $responce = ['status' => false, 'message' => $e->getMessage()];
        }
        return $responce;
    }
    
    /**
     * Mangage imagess of lisitng.
     *
     * @param  int  $id of gallery
     * @return \Illuminate\Http\Response
     */
    public function imageUpdate(Request $request, $id)
    {
        try {
            $post = Post::findOrFail($id);
            $requestData = $request->all();
            dd($requestData);
            $post->fill($requestData);
            $post->save();
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError($e->getMessage())->withInput();
        }

        return back()->with('success', 'Post has been updated successfully.');
    }

    /*
     * upload images
     */
    public function uploadImage(Request $request)
    {
        $path = $this->image_upload_path;
        if ($request->ajax()) {
            $fileData = $_FILES;
            if (isset($fileData['file']) && strlen($fileData['file']["name"]) > 1) {
                $ext  = pathinfo($fileData['file']["name"], PATHINFO_EXTENSION);
                if ($request->postType == 2) {
                    $allowedExt = ['mp4'];
                } else {
                    $allowedExt     = config('constants.ALLOWED_EXT');
                }
                $image_only     = true;

                if (in_array(strtolower($ext), $allowedExt)) {
                    
                    $path = $request->file->store('posts', ['disk' =>'s3', 'visibility' => 'public']);
                    $url = Storage::disk('s3')->url($path);

                    if ($image_only)
                        $data['image'] = true;
                        $data['filename'] = basename($path);
                        $data['image_path'] = $url;
                        $data['success'] = true;
                        $data['message'] = "file successfully uploaded!";
                } else {
                    $data['success'] = false;
                    $data['message'] = "invalid file type!";
                }
            } else {
                $data['success'] = false;
                $data['message'] = "invalid file!";
            }
        }
        return response()->json($data);
    }

}
