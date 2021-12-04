<?php

namespace app\middleware;

use Webman\Http\Request;
use Webman\Http\Response;

/**
 * 检查是否登录的中间件
 */
class CheckAuth implements \Webman\MiddlewareInterface
{
    public function process(Request $request, callable $next): Response
    {
        $session = $request->session();
        if ($session->get("username") !== null) { // 如果 session 中有信息
            return $next($request);
        }

        return NotLoginResponse();
    }
}