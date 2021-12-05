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
        $sender_id = $body['sender_id'];
        $sender_name = $body['sender_name'];
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
}