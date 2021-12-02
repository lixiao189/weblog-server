<?php
namespace app\controller;

use support\Response;

class User
{
    public function login(): Response
    {
        return responseData(0, "登录成功", null);
    }

    public function registerUser(): Response
    {
        return responseData(0, "注册成功", null);
    }
}