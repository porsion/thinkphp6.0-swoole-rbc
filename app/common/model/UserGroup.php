<?php 
namespace app\common\model;

class UserGroup extends \think\Model 
{
    protected $pk = 'auto_id';
    protected $table = 'user_group';
    protected $autoWriteTimestamp = 'create_time';
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
        go(function() use ( $mode ){
            try{
                app(\app\common\model\UserGroupMenu::class)::where('group_id',$mode->auto_id)->delete();
                self::$oplog_type = 'delete';
                event('Oplog',$mode );
            }
            catch (\Exception $e) {
               // event('SysErrorLog',$e);
            }
        });
       
        
    }

    /**
     * 删除之前
     */
    public static function onBeforeDelete( $mode )
    {
        $ret = app(\app\common\model\UserGroupMap::class)->where('group_id',$mode->auto_id)->count();
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