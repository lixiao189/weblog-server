<?php

namespace app\controller;

use support\Db;
use support\Request;
use support\Response;

class Follow
{

    /**
     * 添加关注信息
     * @param Request $request
     * @param int $id
     * @return Response
     */
    function followUser(Request $request, int $id): Response
    {
        $session = $request->session();
        $follower_id = intval($session->get('id'));

        Db::table('follow')->insert([[
            'user_id' => $id,
            'follower_id' => $follower_id,
        ]]);

        // 先添加自己的关注的大佬人数
        Db::connection()->enableQueryLog();
        Db::table('users')->where('id', '=', $follower_id)
            ->update(['followed_num' => Db::raw('followed_num + 1')]);

        // 添加被关注的大佬的跟随者人数
        Db::table('users')->where('id', '=', $id)
            ->update(['followers_num' => Db::raw('followers_num + 1')]);

        return responseData(0, '关注成功', null);
    }


    /**
     * 获取关注列表
     * @param Request $request
     * @return Response
     */
    function getFollowList(Request $request): Response
    {
        $body = json_decode($request->rawBody(), true);

        $type = $body['type'];
        $userID = $body['user_id'];
        $page = intval($body['page']);

        if ($type == 'fans') {
            $result = Db::table('follow')
                ->where('user_id', '=', $userID)
                ->join('users', 'users.id', '=', 'follow.follower_id')
                ->offset(($page - 1) * 20)->limit(20)
                ->get();
            $hasNext = Db::table('follow')
                ->where('user_id', '=', $userID)
                ->join('users', 'users.id', '=', 'follow.follower_id')
                ->offset($page * 20)->limit(20)
                ->count() > 0;
        } else if ($type == 'follow') {
            $result = Db::table('follow')
                ->where('follower_id', '=', $userID)
                ->join('users', 'users.id', '=', 'follow.user_id')
                ->offset(($page - 1) * 20)->limit(20)
                ->get();
            $hasNext = Db::table('follow')
                ->where('follower_id', '=', $userID)
                ->join('users', 'users.id', '=', 'follow.user_id')
                ->offset($page * 20)->limit(20)
                ->count() > 0;
        } else {
            return responseData(1, '参数错误', null);
        }

        $data = array();
        foreach ($result->getIterator() as $user) {
            array_push($data, [
                'id' => $user->id,
                'name' => $user->username,
            ]);
        }

        $respData = [
            "has_next" => $hasNext,
            "list" => $data,
        ];

        if (sizeof($data) == 0) {
            return responseData(2, '没有结果', null);
        } else {
            return responseData(0, '获取成功', $respData);
        }
    }

    /**
     * 取消关注
     * @param Request $request
     * @param int $id
     * @return Response
     */
    function cancelFollow(Request $request, int $id): Response
    {
        $session = $request->session();
        $follower_id = intval($session->get('id'));

        Db::table('follow')->where([
            ['user_id', '=', $id],
            ['follower_id', '=', $follower_id],
        ])->delete();

        // 先减少自己的关注的大佬人数
        Db::table('users')->where('id', '=', $follower_id)->decrement('followed_num');

        // 减少被关注的大佬的跟随者人数
        Db::table('users')->where('id', '=', $id)->decrement('followers_num');

        return responseData(0, '取消关注成功', null);
    }
}