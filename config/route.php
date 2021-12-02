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

use Webman\Route;

Route::group('/user', function () {
    Route::post('/login', [app\controller\User::class, 'login']);
    Route::post('/register', [app\controller\User::class, 'registerUser']);
});

Route::disableDefaultRoute(); // 关闭默认路由
