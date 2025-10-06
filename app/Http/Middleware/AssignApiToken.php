<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AssignApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $authHeader = $request->header('Authorization');
        $token = $request->cookie('API_TOKEN');

        // Authorization ヘッダーが空で、Cookie にAPIトークンがある場合、Authorization ヘッダーにセットする
        if (empty($authHeader) === true && $token !== null) {
            $request->headers->set('Authorization', "Bearer {$token}");
        }

        return $next($request);
    }
}
