<?php

namespace app\controller;

use support\Db;
use support\Request;
use support\Response;

class Post
{
    public function createPost(Request $request): Response
    {
        $body = json_decode($request->rawBody(), true);
        $session = $request->session();

        $sender_id = $session->get('id');
        $sender_name = $session->get('username');
        $title = $body['title'];
        $content = $body['content'];

        Db::table('posts')->insert([[
            'sender_id' => $sender_id,
            'sender_name' => $sender_name,
            'title' => $title,
            'content' => $content,
        ]]);

        return responseData(0, '发送成功', null);
    }

    public function getPost(Request $request, string $id): Response
    {
        $session = $request->session();
        $username = $session->get('username');
        $isAdministrator = $session->get('administrator');

        $result = Db::table('posts')->where('id', '=', $id)->first();

        if (!isset($result)) {
            return responseData(2, '帖子不存在', null);
        } else if ($isAdministrator == false && $result->sender_name != $username) { // 如果非管理员用户看不是自己的帖子
            return responseData(1, '只能查看自己的帖子', null);
        } else {
            return responseData(0, '获取成功', [
                'sender_name' => $result->sender_name,
                'title' => $result->title,
                'content' => $result->content,
                'created_at' => $result->created_at,
            ]);
        }
    }
}