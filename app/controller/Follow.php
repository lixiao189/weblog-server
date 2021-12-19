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
        } else if ($type == 'follow') {
            $result = Db::table('follow')
                ->where('follower_id', '=', $userID)
                ->join('users', 'users.id', '=', 'follow.user_id')
                ->offset(($page - 1) * 20)->limit(20)
                ->get();
        } else {
            return responseData(1, '参数错误', null);
        }

        $resultData = array();
        foreach ($result->getIterator() as $user) {
            array_push($resultData, [
                'id' => $user->id,
                'name' => $user->username,
            ]);
        }

        if (sizeof($resultData) == 0) {
            return responseData(2, '没有结果', null);
        } else {
            return responseData(0, '获取成功', $resultData);
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

        return responseData(0, '取消关注成功', null);
    }
}