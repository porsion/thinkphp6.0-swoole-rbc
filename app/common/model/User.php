<?php 
namespace app\common\model;
use think\facade\Db;

class User extends \think\Model 
{
        protected $pk = 'auto_id';
        protected $table ="user";
        protected $updateTime = 'last_login_time';
        
        public function checkPwd( ? string $password = null) : bool
        {
            if( is_null($password) ) return false;
            return \password_verify($password,$this->password);
        }

        public function pri() : array
        {
            $res = [];
            $group_id = [];
            $ret = Db::table('user_group_map')->alias('map')->where('map.uid',$this->uid)->order('map.join_time','asc')
                        ->leftJoin('group_privileges_map pri_map','map.group_id = pri_map.group_id')
                        ->leftJoin('user_privileges pri','pri.auto_id = pri_map.pri_id')
                        ->field('pri.url,pri.auto_id,map.group_id')
                        ->select()->toArray();
            if($ret)
            {
                foreach($ret as $k=>$v)
                {
                    if( isset($v['url']) )
                    $res[] = format_url( $v['url'] );
                    $group_id[] = $v['group_id'];
                }
            }
            if( $group_id && is_array($group_id))
            {
                $group_id = array_unique($group_id);
            }
            return ['pris'=> array_unique($res),'group_ids'=> $group_id];
        }



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

  

    public function getStatusAttr( $e )
    {
        return $e === 0 ? '正常' : $e;
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