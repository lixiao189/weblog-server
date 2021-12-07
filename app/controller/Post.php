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


    /**
     * @param Request $request
     * @param string $page
     * @return Response
     */
    public function getPostList(Request $request, string $page): Response
    {
        // 处理 session
        $session = $request->session();
        $username = $session->get('username');
        // 解析传输的 json 数据
        $body = json_decode($request->rawBody(), true);
        $page_num = intval($page);

        if ($body['type'] == 'all') {  // 获取所有的帖子
            $posts = Db::table('posts')->offset(($page_num - 1) * 20)->limit(20)->get();
        } else if ($body['type'] == 'user') { // 仅仅获取用户自己的帖子
            if (!isset($username)) {
                return NotLoginResponse();
            } else {
                $posts = Db::table('posts')->where('sender_name', '=', $username)->offset(($page_num - 1) * 20)->limit(20)->get();
            }
        }

        if (!isset($posts)) { // 没有查询到结果
            return responseData(1, '参数错误', null);
        }

        $data = array();
        foreach ($posts->getIterator() as $post) {
            array_push($data, [
                'id' => $post->id,
                'sender_id' => $post->sender_id,
                'sender_name' => $post->sender_name,
                'title' => $post->title,
                'content' => mb_substr($post->content, 0, 100, 'utf-8') . (iconv_strlen($post->content) > 100 ? '...' : ''),
                'created_at' => $post->created_at
            ]);
        }

        if (sizeof($data) == 0) {
            return responseData(2, '没有结果', null);
        } else {
            return responseData(0, '获取成功', $data);
        }
    }
}