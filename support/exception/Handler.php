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
namespace support\exception;

use support\exception\user\DuplicateUserException;
use Webman\Http\Request;
use Webman\Http\Response;
use Throwable;
use Webman\Exception\ExceptionHandler;

/**
 * Class Handler
 * @package support\exception
 */
class Handler extends ExceptionHandler
{
    private array $customExceptionMsg = [
        "用户注册过了"
    ];

    public $dontReport = [
        BusinessException::class,
        DuplicateUserException::class,
    ];

    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    public function render(Request $request, Throwable $exception) : Response
    {
        if (in_array($exception->getMessage(), $this->customExceptionMsg)) {
            return responseData(1, $exception->getMessage(), null);
        }
        return parent::render($request, $exception);
    }

}