<?php
// 初始化部分对象
namespace app;

// 加载 env 文件
$dotenv = \Dotenv\Dotenv::createImmutable(base_path());
$dotenv->safeLoad();
