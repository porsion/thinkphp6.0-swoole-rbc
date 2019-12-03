<?php
namespace app\common\model; 
class GroupPrivilegesMap extends \think\Model 
{
    
        protected $autoWriteTimestamp = false;
        protected $updateTime = false;
        protected $pk = 'auto_id';
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
        event('Oplog', $mode);
    }

     /**
     * 获取操作类型
     */
    public static function getOplogType() : string
    {
        return self::$oplog_type;
    }


}