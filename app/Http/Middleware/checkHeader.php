<?php

namespace App\Http\Middleware;

use Closure;
use App\Traits\ApiGlobalFunctions;

class checkHeader {

    use ApiGlobalFunctions;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
		return $next($request);
        $authInformation = apache_request_headers();
        if (isset($authInformation['Version-Code']) && isset($authInformation['Device-Type']) && $authInformation['Device-Type'] == 'iOS') {
            if (!empty($authInformation['Version-Code']) && $authInformation['Version-Code'] == 1) {
                return $next($request);
            } else {
                return $this->sendError($this->messageDefault('A new version is available. Please update the app.'),'', '402');
            }
        } else if (isset($authInformation['Version-Code']) && isset($authInformation['Device-Type']) && $authInformation['Device-Type'] == 'android') {
            if (!empty($authInformation['Version-Code']) && $authInformation['Version-Code'] == 1) {
                return $next($request);
            } else { 
                return $this->sendError($this->messageDefault('A new version is available. Please update the app.'),'', '402');
            }   
        }else if (isset($authInformation['Version-Code']) && isset($authInformation['Device-Type']) && $authInformation['Device-Type'] == 'web') {
            if (!empty($authInformation['Version-Code']) && $authInformation['Version-Code'] == 1) {
                return $next($request);
            } else { 
                return $this->sendError($this->messageDefault('A new version is available. Please update the app.'),'', '402');
            }   
        } else {

            return $this->sendError($this->messageDefault('You are not authorize to access.') ,'','401');
        }
    }

}
