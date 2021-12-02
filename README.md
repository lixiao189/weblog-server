# weblog-server 📦

## 介绍 📄

一个使用 webman 框架搭建的博客系统后端 (为啥计算机专业的课设都这么累人

和一般的 PHP 后端不同，这个后端是常驻内存型哒

## 如何开始 🤔️

创建对应名称数据库 (数据库名的设置在 `.env` 文件中)

执行一下命令创建表
```bash
vendor/bin/phinx migrate -e [环境]
```
这个地方环境可以选择 `production` 或者 `development` 对应了迁移框架 phinx 的配置文件 
phinx.php (在项目根目录下) 中的 `environments.production` 和 `environments.devleopment` 
字段存储的配置

安装依赖
```bash
composer install 
```

创建环境变量文件
```bash
cp example.env .env
```

由于本项目采用 PHP 脚本模式运行，启动整个项目只需要在根目录下执行 
```bash
php start.php start 
```

## 数据库说明 📔

使用 mysql 数据库，项目中的数据库配置信息在环境变量文件 `.env` 文件中

