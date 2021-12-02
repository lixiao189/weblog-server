<?php
namespace app\controller;

use support\exception\user\DuplicateUserException;
use support\Request;
use support\Response;

class User
{
    public function login(): Response
    {
        return responseData(0, "登录成功", null);
    }

    public function registerUser(Request $request): Response
    {
        // 解析请求数据
        $body = json_decode($request->rawBody(), true);
        $username = $body['username'];
        $password = $body['password'];

        $user = new \app\model\User($username, $password);
        try {
            $user->insertToDB();
        } catch (\PDOException $exception) {
            if (strpos($exception->getMessage(), "Duplicate entry"))
                throw new DuplicateUserException("用户注册过了");
        }


        return responseData(0, "注册成功", null);
    }
}