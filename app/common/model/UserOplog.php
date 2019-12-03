<?php 
namespace app\common\model;
class UserOplog extends \think\Model 
{
    protected $pk = 'auto_id';
    protected $updateTime = false;

    const MODEL = [
        'insert'        => '新增',
        'update'        => '更新',
        'delete'        => '删除'
    ];

    const TABLE = [
        'group_privileges_map'  => '权限与角色关系',
        'menu'                  => '公用菜单',
        'system_config'         => '系统设置',
        'user_error_log'        => '用户操作错误记录',
        'user_group'            => '用户组',
        'user'                  => '用户',
        'user_group_map'        => '用户与组的关系',
        'user_group_menu'       => '用户组菜单',
        'user_login_log'        => '用户登录记录',
        'user_oplog'            => '操作日志',
        'user_privileges'       => '权限路由',
        'config'                => '系统设置'
    ];


    public function getModeAttr( $r )
    {
        return self::MODEL[$r];
    }

    public function getTableAttr( $e )
    {
        return self::TABLE[$e];
    }
}