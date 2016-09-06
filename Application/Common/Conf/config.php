<?php
return array(
    //设置允许访问的模块列表
    'MODULE_ALLOW_LIST'    =>    array('Home','Admin','Api'),
    //设置默认模块
    'DEFAULT_MODULE'     => 'Home', //默认模块 现在访问Home模块的任意控制器均可省略 /Home
    //设置伪静态后缀为空
    'URL_HTML_SUFFIX'=>'',
    //Linux下 URL不区分大小写
    //'URL_CASE_INSENSITIVE' =>true,
    'URL_MODEL' => '2',
    'URL_ROUTER_ON'   => true,

    'PER_PAGE_NUMBER' => 10,

    'DB_TYPE'               =>  'mysql',            // 数据库类型
    'DB_HOST'               =>  'localhost', // 服务器地址
    'DB_NAME'               =>  'fsc_dev',          // 数据库名
    'DB_USER'               =>  'root',              // 用户名
    'DB_PWD'                =>  '',              // 密码
    'DB_PORT'               =>  '3306',             // 端口

    'USER_AUTH_GATEWAY' => '/Admin/Login',          // 默认认证网关


    // 定义加密密钥
    'SECURE_KEY'=> 'C99645A0C22AEE5E4436617DDCF4A1CB',

    //用户的默认等级
    'DEFAULT_USER_LEVEL' => 0,

    //学生的默认等级
    'DEFAULT_STUDENT_LEVEL' => 10,

    //家长的默认等级
    'DEFAULT_PARENT_LEVEL' => 50,


    //可登录后台的用户权限等级（100-499为普通老师）

    'DEFAULT_TEACHER_LEVEL' => 100,

    'LOGIN_ADMIN_LEVEL' => 100,

    'LIST_NEWS_LEVEL' => 500,
    //可发布新闻等级（500-999为信息管理员）
    'ADD_NEWS_LEVEL' => 500,
    //可修改新闻等级
    'MOD_NEWS_LEVEL' => 500,
    //可删除新闻等级
    'DELETE_NEWS_LEVEL' => 500,
    //可永久删除新闻等级
    'REMOVE_NEWS_LEVEL' => 500,
    //可发布活动等级
    'ADD_ACTIVITY_LEVEL' => 600,
    //可修改删除新闻等级
    'MOD_ACTIVITY_LEVEL' => 600,

    //可修改用户信息等级
    'MOD_USER_MESSAGE_LEVEL' => 800,


    //可修改学校信息等级
    'MOD_SCHOOL_MESSAGE_LEVEL' => 900,

    //可增加用户的权限等级（1000-1500为账号管理员）
    'ADD_USER_ACCOUNT_LEVEL' => 1000,
    'MOD_USER_ACCOUNT_LEVEL' => 1000,
    //可导入的用户等级
    'IMPORT_LEVEL' => 1200

);