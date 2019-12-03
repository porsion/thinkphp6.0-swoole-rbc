<?php 
return [
    /**
     * 不需要验证是否登录的url
     */
    'do_not_check_login_url'  => [
        '/admin/login.htm',
       // '/admin/user/insert.htm',
    ],

    /**
     * 是否全局开启权限验证
     */
    'is_auth_open'  => true,

    /**
     * 不受权限验证的用户ID 注意，是uid，不是auto_id
     */
    'sikp_rule_auth_uids'   => ['e10adc3949ba59abbe56e057f20f883e'],
];