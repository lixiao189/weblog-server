<?php

namespace app\controller;

use support\Db;
use support\exception\user\DuplicateUserException;
use support\Request;
use support\Response;

class User
{
    public function login(Request $request): Response
    {
        // 解析请求数据
        $body = json_decode($request->rawBody(), true);
        $username = $body['username'];
        $password = $body['password'];

        $result = Db::table('users')->where('username', $username)->first();
        if ($result == null) {
            return responseData(2, '用户不存在', null);
        } else if ($password != $result->password) {
            return responseData(1, '密码错误', null);
        } else {
            // 设置 session
            $session = $request->session();
            $session->set("username", $username);
            $session->set("identity", $result->identity);

            return responseData(0, '登录成功', null);
        }
    }

    public function registerUser(Request $request): Response
    {
        // 解析请求数据
        $body = json_decode($request->rawBody(), true);
        $username = $body['username'];
        $password = $body['password'];

        try {
            Db::table('users')->insert([
                "username" => $username,
                "password" => $password,
                "identity" => false,
            ]);
        } catch (\PDOException $exception) {
            if (strpos($exception->getMessage(), "Duplicate entry"))
                throw new DuplicateUserException("用户注册过了");
        }


        return responseData(0, "注册成功", null);
    }
}