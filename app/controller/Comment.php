<?php

namespace app\controller;

use support\Db;
use support\Request;
use support\Response;

class Comment
{
    /**
     * 创建评论
     * @param Request $request
     * @return Response
     */
    public function createComment(Request $request): Response
    {
        // 解析传输的 json 数据
        $body = json_decode($request->rawBody(), true);
        $postID = $body['post_id'];
        $content = $body['content'];
        $atID = $body['at_id'];
        $atName = $body['at_name'];

        // 解析用户 session
        $session = $request->session();
        $senderID = $session->get('id');
        $senderName = $session->get('username');

        Db::table('comments')->insert([[
            'post_id' => $postID,
            'content' => $content,
            'at_id' => $atID,
            'at_name' => $atName,
            'sender_id' => $senderID,
            'sender_name' => $senderName,
        ]]);

        return responseData(0, '创建成功', null);
    }


    /**
     * 获取评论列表
     * @param Request $request
     * @return Response
     */
    function getCommentList(Request $request): Response
    {
        $body = json_decode($request->rawBody(), true);



        return responseData(0, '获取成功', null);
    }
}