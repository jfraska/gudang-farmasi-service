<?php

namespace App\Http\Middleware;

use App\Services\Auth;
use Closure;
use Illuminate\Support\Facades\Redis;

class APIMiddleware
{
    public $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        if ($request->hasHeader('Authorization')) {
            // if ($user_id = Redis::get('user:' . $request->bearerToken() . ':' . $permission)) {
            //     $request->merge(['user_id' => $user_id]);
            //     return $next($request);
            // }
            $data = $this->auth->userCan("Bearer " . $request->bearerToken(), $permission);
            if ($data['code'] == 200) {
                $request->merge(['user_id' => $data['data']->user_id]);
                // Redis::set('user:' . $request->bearerToken() . ':' . $permission, $data['data']->user_id);
                return $next($request);
            }
        }

        return abort(403, 'Forbidden');
    }
}
