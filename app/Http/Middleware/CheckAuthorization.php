<?php



namespace App\Http\Middleware;



use Closure;

use App\Traits\ApiGlobalFunctions;
use App\Models\User;
use DB;



class CheckAuthorization {

    use ApiGlobalFunctions;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next) {
        $authInformation = apache_request_headers();

        if (isset($authInformation['Authorization']) && !empty($authInformation['Authorization'])) {

            $token = str_replace("Bearer", " ", $authInformation['Authorization']);
            $token = trim($token);
            $user = User::where('api_token', $token)->first();
            if (empty($user)) {
                return $this->sendError($this->messageDefault('You are not authorize to access any more.'),'','401');
            } else {
                if ($user->status == 0) {
                    return $this->sendError($this->messageDefault('You account has been deactivated,Please contact the admin.'),'', '401');
                } else {
                    $request->attributes->set('Auth', $user);
                    // \Auth::login($user);
                    return $next($request);
                }
            }
        } else {
            return $this->sendError($this->messageDefault('You are not authorize to access.'),'', '401');
        }
    }
}
