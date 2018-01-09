<?php

return array(
	'MODULE_ALLOW_LIST'      =>  array('Home','Admin'),
	'DEFAULT_MODULE'         =>  'Home', 
	'VERSION'                =>  '1.0.0',
	'URL_MODEL'			     =>  2,
	'URL_CASE_INSENSITIVE'   => false, 
	'SHOW_PAGE_TRACE'        => false, 


	'DB_TYPE'               => 'mysql', 
    'DB_HOST'                => '127.0.0.1',
    'DB_NAME'               => 'yike', 
    'DB_USER'               => 'root', 
    'DB_PWD'                 => 'root', 
    
//     'DB_TYPE'               => 'mysql',
//     'DB_HOST'                => 'rm-bp12mc100srz6vime.mysql.rds.aliyuncs.com',
//     'DB_NAME'               => 'rw6y77152a',
//     'DB_USER'               => 'rw6y77152a',
//     'DB_PWD'                 => 'Wgw5317645',

    

	'DB_PORT'               => '3306',
    'DB_PREFIX'             => 'mz_',
    
    'TOKEN_ON'      =>    true,  // 是否开启令牌验证 默认关闭
    'TOKEN_NAME'    =>    '__hash__',    // 令牌验证的表单隐藏字段名称，默认为__hash__
    'TOKEN_TYPE'    =>    'md5',  //令牌哈希验证规则 默认为MD5
    'TOKEN_RESET'   =>    true,  //令牌验证出错后是否重置令牌 默认为true
    
// //默认错误跳转对应的模板文件
'TMPL_ACTION_ERROR'     =>  '../../../Public/Admin/error',
// //默认成功跳转对应的模板文件
'TMPL_ACTION_SUCCESS'   =>  '../../../Public/Admin/success',
);