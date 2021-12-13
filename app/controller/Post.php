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
        $result = Db::table('posts')->where('id', '=', $id)->first();

        if (!isset($result)) {
            return responseData(2, '帖子不存在', null);
        } else {
            return responseData(0, '获取成功', [
                'sender_id' => $result->sender_id,
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
        // 解析传输的 json 数据
        $body = json_decode($request->rawBody(), true);
        $page_num = intval($page);

        if ($body['type'] == 'all') {  // 获取所有的帖子
            $posts = Db::table('posts')->orderBy('created_at', 'desc')->offset(($page_num - 1) * 20)->limit(20)->get();
        } else if ($body['type'] == 'user') { // 仅仅获取用户自己的帖子
            $id = $body['id'];
            $posts = Db::table('posts')->orderBy('created_at', 'desc')->where('sender_id', '=', $id)
                ->offset(($page_num - 1) * 20)->limit(20)->get();
        }

        if (!isset($posts)) { // 因为参数错误没有查询到结果
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

    public function deletePost(Request $request, int $id): Response
    {
        // session 信息解析
        $session = $request->session();
        $sender_id = intval($session->get('id'));

        $result = Db::table('posts')->where('id', '=', $id)->first();
        if (!isset($result)) {
            return responseData(1, '数据不存在', null);
        } else if ($result->sender_id !== $sender_id) {
            return responseData(2, '只能删除自己的帖子', null);
        }

        Db::table('posts')->delete($id);

        return responseData(0, '删除成功', null);
    }

    public function modifyPost(Request $request): Response
    {
        // 解析传输的 json 数据
        $body = json_decode($request->rawBody(), true);
        $id = $body['id']; // 帖子的 ID
        $title = $body['title'];
        $content = $body['content'];

        // 解析 session 数据
        $session = $request->session();
        $user_id = intval($session->get('id'));

        $result = Db::table('posts')->where('id', '=', $id)->first();

        if (!isset($result)) {
            return responseData(1, '帖子不存在', null);
        } else if ($result->sender_id !== $user_id) {
            return responseData(2, '只能修改自己的帖子', null);
        }

        Db::table('posts')->where('id', '=', $id)->update([
            'title' => $title,
            'content' => $content,
        ]);

        return responseData(0, '修改成功', null);
    }
}