<?php 
namespace app\common\model;
use think\helper\Str;
use think\Model;
class Config extends Model 
{
    protected $pk = 'k';
    protected $table = 'system_config';

    protected $autoWriteTimestamp = false;
    protected $updateTime = false;
    protected static $oplog_type;

    public function getNotCheckLoginUrl(string $data = null ) : array
    {
        if( !is_null($data) )
        {
            $urls = [];
            $data = array_unique(\explode('|',$data));
            foreach( $data as $v )
            {
                $urls[] = format_url($v);
            }
            return $urls;
        }
        return [];
    }


    /**
     * 新增之前
     */
    public static function onBeforeInsert( $mode )
    {
       
        if( $mode::find($mode->k) )
        {
            return false;
        }
    }

    /**
     * 新增之后
     */
    public static function onAfterInsert( $mode )
    {
        self::$oplog_type = 'insert';
        event('Oplog',$mode);
    }

    public static function onAfterDelete( $mode )
    {
        self::$oplog_type = 'delete';
        event('Oplog',$mode);
    }

    public static function getOplogType() : string 
    {
        return self::$oplog_type;
    }
}
