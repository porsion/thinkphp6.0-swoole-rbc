<?php 
namespace app\common\model;
class UserPrivileges extends \think\Model 
{

    protected $pk = 'auto_id';
    protected $table = 'user_privileges';
    protected $autoWriteTimestamp = false;
    
    protected $updateTime = false;
    /**
     * 操作类型
     * 用于触发事件类
     */
    private static $oplog_type;

    /**
     * 新增之前
     */
    public static function onBeforeInsert( $mode )
    {
            $is = $mode::where('url',$mode->url)->count();
            if( $is > 0 )
            {
                return false;
            }
    }
    /**
     * 新增完成后
     */
    public static function onAfterInsert( $mode )
    {
        self::$oplog_type = 'insert';
        event('Oplog', $mode);
    }

    /**
     * 更新之前
     */
    public static function onBeforeUpdate( $mode )
    {
        $is = $mode::where('url',$mode->url)->where('auto_id','<>',$mode->auto_id)->count();
        if( $is > 0 )
        {
            return false;
        }
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