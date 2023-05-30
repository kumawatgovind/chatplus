<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if($request->route()->getPrefix() == 'api'){
            return true;
        }else{
            if (!$request->expectsJson()) {
                $prefix = str_replace("/", "", $request->route()->getPrefix());
                if($request->is('admin') || $request->is('admin/*')){
                    return route('admin.login');
                }
                return route('frontend.login');
            } else {
                return ['status' => false, 'message' => 'Your login session has expired.'];
                die;
            }
        }
    }
}
