<?php 
namespace app\common\model;
class UserGroupMenu extends \think\Model 
{

    protected $pk = 'auto_id';
    protected $table = 'user_group_menu';
    protected $autoWriteTimestamp = false;
    
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
    public static function onAfterDelete( $mode ) : void
    {
        self::$oplog_type = 'delete';
        event('Oplog',$mode );
    }

    /**
     * 更新完成后
     */
    public static function onAfterUpdate( $mode ) : void
    {
        self::$oplog_type = 'update';
        event('Oplog',$mode );
    }

    /**
     * 获取操作类型
     */
    public static function getOplogType( ) : string
    {
        return self::$oplog_type;
    }

    public static function setOplogType(string $e):void
    {
        self::$oplog_type = $e;
    }
  
}