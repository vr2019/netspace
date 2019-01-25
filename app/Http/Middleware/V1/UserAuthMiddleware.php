<?php

namespace App\Http\Middleware\v1;

use Closure;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use GuzzleHttp\Client;

class UserAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $usercheckurl = env('USER_URL');
        $routerx = explode('?', $request->GetRequestUri())[0];

        $rts = $request->route();
        if(count($rts) < 2 || !isset($rts[1]['as'])){
            throw new UnauthorizedHttpException('no auth');
        }
        $routename = $rts[1]['as'];

        //TODO:: 检查权限
        $headers = $request->server->getHeaders();
        $token = $headers['AUTHORIZATION'];

        $headers = ['Authorization'=>$token];
        $client = new Client(['base_uri' => $usercheckurl]);
        $response = $client->request('GET', 'auth/isauth', ['headers'=>$headers]);
        $content = $response->getBody()->getContents();

        $user = json_decode($content);
        if(!$user || !$user->user){
            throw new UnauthorizedHttpException('no auth');
        }
        $user = $user->user;

        $request->merge(array('userid'=>$user->id));

        return $next($request);
    }
}