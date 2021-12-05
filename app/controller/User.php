<?php

namespace app\controller;

use PDOException;
use support\Db;
use support\exception\user\DuplicateUserException;
use support\Request;
use support\Response;

class User
{
    /**
     * 用户登录
     * @param Request $request
     * @return Response
     */
    public function login(Request $request): Response
    {
        // 解析请求数据
        $body = json_decode($request->rawBody(), true);
        $username = $body['username'];
        $password = $body['password'];

        $result = Db::table('users')->where('username', '=', $username)->first();
        if ($result == null) {
            return responseData(2, '用户不存在', null);
        } else if ($password != $result->password) {
            return responseData(1, '密码错误', null);
        } else {
            // 设置 session
            $session = $request->session();
            $session->set('id', $result->id);
            $session->set('username', $username);
            $session->set('administrator', $result->administrator);

            return responseData(0, '登录成功', null);
        }
    }

    /**
     * 注册用户
     * @param Request $request
     * @return Response
     */
    public function registerUser(Request $request): Response
    {
        // 解析请求数据
        $body = json_decode($request->rawBody(), true);
        $username = $body['username'];
        $password = $body['password'];

        // 判断用户名长度
        if (strlen($username) < 4 || strlen($username) > 16 || strlen($password) < 7 || strlen($password) > 20)
            return responseData(2, "参数错误", null);

        try {
            Db::table('users')->insert([
                "username" => $username,
                "password" => $password,
                "administrator" => false,
            ]);
        } catch (PDOException $exception) {
            if (strpos($exception->getMessage(), "Duplicate entry"))
                throw new DuplicateUserException("该用户名注册过了");
        }


        return responseData(0, "注册成功", null);
    }

    /**
     * 获取用户信息
     * @param Request $request
     * @return Response
     */
    public function getInfo(Request $request): Response
    {
        $session = $request->session();
        $id = $session->get('id');

        // 查找用户信息
        $user = Db::table('users')->where('id', '=', $id)->first();

        return responseData(0, '查询成功', [
            'id' => $user->id,
            'username' => $user->username,
            'administrator' => $user->administrator == true,
        ]);
    }


    /**
     * 修改用户信息
     * @param Request $request 用户请求
     * @return Response 响应数据
     */
    public function update(Request $request): Response
    {
        return responseData(0, '', null);
    }

    /**
     * @param Request $request 请求数据
     * @return Response 响应数据
     */
    public function logout(Request $request): Response
    {
        $session = $request->session();
        $session->flush();

        return responseData(0, '删除成功', null);
    }
}