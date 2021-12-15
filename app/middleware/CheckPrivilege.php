<?php

namespace app\middleware;

use Webman\Http\Request;
use Webman\Http\Response;

class CheckPrivilege implements \Webman\MiddlewareInterface
{

    /**
     * @inheritDoc
     */
    public function process(Request $request, callable $next): Response
    {
        $session = $request->session();
        if ($session->get("username") !== null) { // 如果 session 中有信息
            // 检查管理员权限
            $isAdministrator = $session->get('administrator') == 1;
            if ($isAdministrator)
                return $next($request);
            else
                return NotAdministratorResponse();
        }

        return NotLoginResponse();
    }
}