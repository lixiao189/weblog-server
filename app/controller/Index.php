<?php
namespace app\controller;

use support\Request;
use Support\Response;

class Index
{
    public function index(Request $request): Response
    {
        return response('Welcome to the weblog');
    }
}
