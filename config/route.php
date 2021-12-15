<?php
/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

use app\middleware\CheckAuth;
use Webman\Route;

Route::group('/api', function () {
    // 用户接口 API
    Route::group('/user', function () {
        Route::post('/login', [app\controller\User::class, 'login']);
        Route::post('/register', [app\controller\User::class, 'registerUser']);
        Route::post('/info', [app\controller\User::class, 'getInfo']);
        Route::post('/update', [app\controller\User::class, 'update'])->middleware([CheckAuth::class]);
        Route::get('/logout', [app\controller\User::class, 'logout'])->middleware([CheckAuth::class]);
    });

    // 帖子接口 API
    Route::group('/post', function () {
        Route::post('/create', [app\controller\Post::class, 'createPost'])->middleware([CheckAuth::class]);
        Route::get('/{id}', [app\controller\Post::class, 'getPost']);
        Route::get('/delete/{id}', [app\controller\Post::class, 'deletePost']);
        Route::post('/list/{page}', [app\controller\Post::class, 'getPostList']);
        Route::post('/modify', [app\controller\Post::class, 'modifyPost'])->middleware([CheckAuth::class]);

        // 帖子回复接口 API
        Route::group('/comment', function () {
            Route::post('/create', [app\controller\Comment::class, 'createComment'])->middleware([CheckAuth::class]);
            Route::get('/delete/{id}', [app\controller\Comment::class, 'deleteComment'])->middleware([CheckAuth::class]);
            Route::post('/list', [app\controller\Comment::class, 'getCommentList']);
        });
    });
});

Route::disableDefaultRoute(); // 关闭默认路由
