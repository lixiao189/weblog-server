<?php

namespace app\controller;

use support\Db;
use support\Request;
use support\Response;

/**
 * 举报帖子的控制器
 */
class Report
{
    function createReport(Request $request): Response
    {
        $body = json_decode($request->rawBody(), true);
        $type = $body['type'];
        $id = intval($body['id']);

        if ($type == 'post') {
            $affected = Db::table('posts')->where('id', '=', $id)->update(['is_reported' => 1]);
            if ($affected == 0)
                return responseData(2, '没有影响', null);
        } else if ($type == 'comment') {
            $affected = Db::table('comments')->where('id', '=', $id)->update(['is_reported' => 1]);
            if ($affected)
                return responseData(2, '没有影响', null);
        } else {
            return responseData(1, '参数错误', null);
        }

        return responseData(0, '举报成功', null);
    }

    function listReport(Request $request): Response
    {
        $body = json_decode($request->rawBody(), true);
        $type = $body['type'];

        if ($type == 'post') {
            // 拉取被举报的帖子
            $posts = Db::table('posts')->orderBy('created_at', 'desc')
                ->where('is_reported', '=', 1)->get();
            return postListData($posts->getIterator(), false);
        } else if ($type == 'comment') {
            // 拉取被举报的评论
            $comments = Db::table('comments')->where('is_reported', '=', 1)
                ->get();
            return commentListData($comments->getIterator(), false);
        } else {
            return responseData(1, '参数错误', null);
        }
    }

    function cancelReport(Request $request): Response
    {
        $body = json_decode($request->rawBody(), true);
        $type = $body['type'];
        $id = intval($body['id']);

        if ($type == 'post') {
            $affected = Db::table('posts')->where('id', '=', $id)->update(['is_reported' => 0]);
            if ($affected == 0)
                return responseData(2, '帖子不存在', null);
        } else if ($type == 'comment') {
            $affected = Db::table('comments')->where('id', '=', $id)->update(['is_reported' => 0]);
            if ($affected)
                return responseData(2, '评论不存在', null);
        } else {
            return responseData(1, '参数错误', null);
        }

        return responseData(0, '撤销成功', null);
    }
}