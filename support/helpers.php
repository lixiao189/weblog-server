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

use support\Db;
use support\Request;
use support\Response;
use support\Translation;
use Webman\App;
use Webman\Config;
use Webman\Route;

define('BASE_PATH', realpath(__DIR__ . '/../'));

/**
 * @return string
 */
function base_path()
{
    return BASE_PATH;
}

/**
 * @return string
 */
function app_path()
{
    return BASE_PATH . DIRECTORY_SEPARATOR . 'app';
}

/**
 * @return string
 */
function public_path()
{
    return BASE_PATH . DIRECTORY_SEPARATOR . 'public';
}

/**
 * @return string
 */
function config_path()
{
    return BASE_PATH . DIRECTORY_SEPARATOR . 'config';
}

/**
 * @return string
 */
function runtime_path()
{
    return BASE_PATH . DIRECTORY_SEPARATOR . 'runtime';
}

/**
 * @param int $status
 * @param array $headers
 * @param string $body
 * @return Response
 */
function response($body = '', $status = 200, $headers = array())
{
    return new Response($status, $headers, $body);
}

/**
 * @param $data
 * @param int $options
 * @return Response
 */
function json($data, $options = JSON_UNESCAPED_UNICODE)
{
    return new Response(200, ['Content-Type' => 'application/json'], json_encode($data, $options));
}


/**
 * @param int $code 错误码
 * @param string $msg 提示信息
 * @param array|null $data 承载数据
 * @return Response
 */
function responseData(int $code, string $msg, ?array $data): Response
{
    return json([
        'code' => $code,
        'msg' => $msg,
        'data' => $data,
    ]);
}

/**
 * @param $xml
 * @return Response
 */
function xml($xml)
{
    if ($xml instanceof SimpleXMLElement) {
        $xml = $xml->asXML();
    }
    return new Response(200, ['Content-Type' => 'text/xml'], $xml);
}

/**
 * @param $data
 * @param string $callback_name
 * @return Response
 */
function jsonp($data, $callback_name = 'callback')
{
    if (!is_scalar($data) && null !== $data) {
        $data = json_encode($data);
    }
    return new Response(200, [], "$callback_name($data)");
}

/**
 * @param $location
 * @param int $status
 * @param array $headers
 * @return Response
 */
function redirect($location, $status = 302, $headers = [])
{
    $response = new Response($status, ['Location' => $location]);
    if (!empty($headers)) {
        $response->withHeaders($headers);
    }
    return $response;
}

/**
 * @param $template
 * @param array $vars
 * @param null $app
 * @return Response
 */
function view($template, $vars = [], $app = null)
{
    static $handler;
    if (null === $handler) {
        $handler = config('view.handler');
    }
    return new Response(200, [], $handler::render($template, $vars, $app));
}

/**
 * @return Request
 */
function request()
{
    return App::request();
}

/**
 * @param $key
 * @param null $default
 * @return mixed
 */
function config($key = null, $default = null)
{
    return Config::get($key, $default);
}

/**
 * @param $name
 * @param array $parameters
 * @return string
 */
function route($name, $parameters = [])
{
    $route = Route::getByName($name);
    if (!$route) {
        return '';
    }
    return $route->url($parameters);
}

/**
 * @param null $key
 * @param null $default
 * @return mixed
 */
function session($key = null, $default = null)
{
    $session = request()->session();
    if (null === $key) {
        return $session;
    }
    if (\is_array($key)) {
        $session->put($key);
        return null;
    }
    return $session->get($key, $default);
}

/**
 * @param null|string $id
 * @param array $parameters
 * @param string|null $domain
 * @param string|null $locale
 * @return string
 */
function trans(string $id, array $parameters = [], string $domain = null, string $locale = null)
{
    $res = Translation::trans($id, $parameters, $domain, $locale);
    return $res === '' ? $id : $res;
}

/**
 * @param null|string $locale
 * @return string
 */
function locale(string $locale = null)
{
    if (!$locale) {
        return Translation::getLocale();
    }
    Translation::setLocale($locale);
}

/**
 * @param $worker
 * @param $class
 */
function worker_bind($worker, $class)
{
    $callback_map = [
        'onConnect',
        'onMessage',
        'onClose',
        'onError',
        'onBufferFull',
        'onBufferDrain',
        'onWorkerStop',
        'onWebSocketConnect'
    ];
    foreach ($callback_map as $name) {
        if (method_exists($class, $name)) {
            $worker->$name = [$class, $name];
        }
    }
    if (method_exists($class, 'onWorkerStart')) {
        call_user_func([$class, 'onWorkerStart'], $worker);
    }
}

/**
 * @return int
 */
function cpu_count()
{
    // Windows does not support the number of processes setting.
    if (\DIRECTORY_SEPARATOR === '\\') {
        return 1;
    }
    if (strtolower(PHP_OS) === 'darwin') {
        $count = shell_exec('sysctl -n machdep.cpu.core_count');
    } else {
        $count = shell_exec('nproc');
    }
    $count = (int)$count > 0 ? (int)$count : 4;
    return $count;
}

function NotLoginResponse(): Response
{
    return responseData(255, '尚未登录', null);
}

function NotAdministratorResponse(): Response
{
    return responseData(254, '不是管理员', null);
}

function postListData(ArrayIterator $postIterator, bool $hasNext): Response
{
    $data = array();
    foreach ($postIterator as $post) {
        array_push($data, [
            'id' => $post->id,
            'sender_id' => $post->sender_id,
            'sender_name' => $post->sender_name,
            'title' => $post->title,
            'content' => mb_substr($post->content, 0, 100, 'utf-8') . (iconv_strlen($post->content) > 100 ? '...' : ''),
            'created_at' => $post->created_at
        ]);
    }

    $resultData = [
        "has_next" => $hasNext,
        "list" => $data,
    ];

    if (sizeof($data) == 0) {
        return responseData(2, '没有结果', null);
    } else {
        return responseData(0, '获取成功', $resultData);
    }
}

function commentListData(ArrayIterator $commentIterator, bool $hasNext): Response
{
    $data = array();
    foreach ($commentIterator as $comment) {
        array_push($data, [
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

    $resultData = [
        "has_next" => $hasNext,
        "list" => $data,
    ];

    if (sizeof($data) == 0) {
        return responseData(2, '没有结果', null);
    } else {
        return responseData(0, '获取成功', $resultData);
    }
}