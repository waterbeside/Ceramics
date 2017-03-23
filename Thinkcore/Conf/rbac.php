<?php
return array(
	/* RBAC 提示：请不要修改。 */
    "USER_AUTH_ON" => true, //是否开启权限认证
    "USER_AUTH_TYPE" => 1, //默认认证类型 1 登录认证 2 实时认证
    "USER_AUTH_KEY" => "AuthUserID", //用户认证SESSION标记，用于保存登陆后用户ID
    'ADMIN_AUTH_KEY' => 'administrator', //高级管理员无需进行权限认证$_SESSION['administrator']=true;
    "REQUIRE_AUTH_MODULE" => "", //需要认证模块
    "NOT_AUTH_MODULE" => "Public", //无需认证模块
    "USER_AUTH_GATEWAY" => "", //认证网关
    'GUEST_AUTH_ON' => false, // 是否开启游客授权访问
    'USER_AUTH_MODEL' => 'User', //用户信息表
);