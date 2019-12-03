<?php
namespace app\common\model;
class Menu extends \think\Model
{
    protected $pk = 'auto_id';
    protected $table = 'menu';
    protected $updateTime = false;

    /**
     * 操作类型
     * 用于触发事件类
     */
    private static $oplog_type;

    /**
     * 新增完成后
     */
    public static function onAfterInsert( $mode )
    {
        self::$oplog_type = 'insert';
        event('Oplog', $mode);
    }


    /**
     * 删除完成后
     */
    public static function onAfterDelete( $mode )
    {
        self::$oplog_type = 'delete';
        event('Oplog',$mode );
    }

    /**
     * 删除之前
     * 需要检测是否有组在使用该菜单
     */
    public static function onBeforeDelete( $mode )
    {
        $ret = app(\app\common\model\UserGroupMenu::class)->where('menu_id',$mode->auto_id)->count();
        if( $ret > 0 )
        {
            return false;
        }
       
    }
    /**
     * 更新完成后
     */
    public static function onAfterUpdate( $mode )
    {
        self::$oplog_type = 'update';
        event('Oplog',$mode );
    }

    /**
     * 获取操作类型
     */
    public static function getOplogType( )
    {
        return self::$oplog_type;
    }
   
}