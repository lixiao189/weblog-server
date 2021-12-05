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
        Route::get('/info', [app\controller\User::class, 'getInfo'])->middleware([CheckAuth::class]);
        Route::get('/update', [app\controller\User::class, 'update'])->middleware([CheckAuth::class]);
    });

    // 发送帖子 API

});

Route::disableDefaultRoute(); // 关闭默认路由
