<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $email = $request->header('email');
        $access_key = $request->header('access_key');

        if($email == null || $access_key == null) {
            return response(json_encode(['status'=>false, 'message'=>'unauthorized']), 401)
                        ->header('Content-Type', 'application/json');
        }
        $findUser = User::where('email', $email)
                ->where('access_key', $access_key)
                ->first();
           // var_dump($findUser);
        if($findUser == null || empty($findUser)) {
        
            return response(json_encode(['status'=>false, 'message'=>'unauthorized']), 401)
                                    ->header('Content-Type', 'application/json');


        }
 
        $request->headers->set('currentUser', $findUser->email);
        //$request->setcookie('currentUser', $findUser);
        return $next($request);
    }
}
