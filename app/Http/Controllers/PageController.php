<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\ApiGlobalFunctions;
use Illuminate\Http\Request;
use App\Models\Page;

use App\Repositories\NotificationRepository;
use Stripe;

class PageController extends Controller
{
    /**
     * getPage
     *
     * @param  mixed $request
     * @return json
     */
    public function getPage($pageSlug)
    {
        $page = Page::where('slug', $pageSlug)->select(['title', 'description'])->first();
        return view('pages.view',compact('page'));
    }

}
