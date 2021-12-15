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

        // 检测 at_id 和 at_name 信息
        if (isset($atID) || isset($atName)) {
            $result = Db::table('users')->where('id', '=', $atID)->first();
            if (!isset($result)) {
                return responseData(1, 'At 用户不存在', null);
            } else if ($result->username !== $atName) {
                return responseData(2, 'At 用户和 ID 无法对应', null);
            }
        }

        // 获取帖子标题
        $result = Db::table('posts')->where('id', '=', $postID)->first();
        $title = $result->title;

        // 解析用户 session
        $session = $request->session();
        $senderID = $session->get('id');
        $senderName = $session->get('username');

        Db::table('comments')->insert([[
            'post_id' => $postID,
            'post_title' => $title,
            'content' => $content,
            'at_id' => $atID,
            'at_name' => $atName,
            'sender_id' => $senderID,
            'sender_name' => $senderName,
            'is_reported' => false,
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
        // 获取 post 数据
        $body = json_decode($request->rawBody(), true);
        $type = $body['type'];
        $id = intval($body['id']);
        $page = intval($body['page']);

        if ($type == 'post') {
            // 获取帖子评论
            $resultIterator = Db::table('comments')->where('post_id', '=', $id)
                ->offset(($page - 1) * 20)->limit(20)->get()->getIterator();
        } else if ($type == 'user') {
            // 获取用户评论
            $resultIterator = Db::table('comments')->where('sender_id', '=', $id)
                ->offset(($page - 1) * 20)->limit(20)->get()->getIterator();
        } else {
            return responseData(1, '参数错误', null);
        }

        $respData = array();
        foreach ($resultIterator as $comment) {
            array_push($respData, [
                'id' => $comment->id,
                'post_id' => $comment->post_id,
                'post_title' => $comment->post_title,
                'content' => $comment->content,
                'sender_id' => $comment->sender_id,
                'sender_name' => $comment->sender_name,
                'at_id' => $comment->at_id,
                'at_name' => $comment->at_name,
                'created_at' => $comment->created_at,
            ]);
        }

        if (sizeof($respData) == 0) {
            return responseData(2, '没有结果', null);
        } else {
            return responseData(0, '获取成功', $respData);
        }
    }

    function deleteComment(Request $request, int $id): Response
    {
        // session 信息解析
        $session = $request->session();
        $sender_id = intval($session->get('id'));
        $isAdministrator = $session->get('administrator') == 1;
        $result = Db::table('comments')->where('id', '=', $id)->first();
        if (!isset($result)) {
            return responseData(1, '数据不存在', null);
        } else if ($result->sender_id !== $sender_id && !$isAdministrator) {
            return responseData(2, '只能删除自己的评论', null);
        }

        Db::table('comments')->delete($id);

        return responseData(0, '删除成功', null);
    }
}