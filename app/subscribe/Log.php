<?php
declare (strict_types = 1);

namespace app\subscribe;
use think\Event;
use think\helper\Str;
class Log
{
    
    /**
     * 用户操作日志
     */
    public  function onOplog( ? object $mode )
    {
       if( !$mode ) return ;
       $user_id = app(\app\Request::class)->user_id;
        go(function() use( $mode,$user_id ){
            $data = 
            [
                'table' => Str::snake(class_basename($mode)),
                'pk'    => $mode->getPk(),
                'pk_value'  => $mode->getKey() ? $mode->getKey() : 0,
                'mode'  => $mode::getOplogType(),
                'uid'   => $user_id,
            ];
            if( $mode::getOplogType() == 'update' )
            {
                $data['data'] = json_encode( $mode->getOrigin(), JSON_UNESCAPED_UNICODE);
            }
          
            app(\app\common\model\UserOplog::class)->save($data);
        });
        
      
    }


    /**
     * 系统错误日志
     */
    public  function onSysErrorLog( \Exception $e )
    {
        go(function() use( $e ){
            $e->getTrace();
            
        });
    }



    /**
     * 用户操作错误时的日志
     */
    public function onUserErrorLog( \app\Request $mode )
    {
         go(function() use( $mode ){
            app(app\common\model\UserErrorLog::class)->insData($mode)->save();
        });
    }    
}
