<?php 
namespace app\middleware\traits;
trait  Auth 
{
    /**
     * 获取不需要检查登录的URL
     */
    public function getNotCheckLoginUrl() : ? array
    {
      return array_unique ( array_merge( 
            self::getBaseDoNotCheckLoginUrl() ,
            (array)system_config('not_check_login_url') 
            ) );
    }

    /**
     * 请求的URL是否在不检查权限的URL之内
     * @return bool
     */
    public function isInNotAuthUrl( ? string $baseUrl = null) : bool 
    {
        $not_url =    (array) system_config('not_auth_url') ;
        return (bool) in_array($baseUrl,$not_url);
    }

    /**
     * 是否开启权限验证
     */
    public function isAuthOpen() : bool 
    {
        return  (bool) config('admin.is_auth_open') === true ;
    }

    /**
     * 请求的用户是否排除在权限验证之外
     */
    public function isSikpRuleAuth( $uid = null) : bool 
    {
        if (is_null($uid)) return false;
        return in_array($uid,(array)config('admin.sikp_rule_auth_uids'));
    }

    /**
     * 验证请求的Url是否有权
     */
    public function authUrlFail( string $id, string $baseUrl ) : bool 
    {
        $auths = static::getUserPris($id);
        if($auths)
        {
            return (bool) !in_array($baseUrl,$auths);
        }
        return true;
    }

    /**
     * 获取最基础的不需要验证登录的URL
     */
    private static function getBaseDoNotCheckLoginUrl() : ? array 
    {
        $baseUrl = (array) \Config('admin.do_not_check_login_url');
        if( $baseUrl )
        {
            foreach( $baseUrl as $k )
            {
                $baseUrl[] = format_url($k);
            }
        }
        return $baseUrl;
    }


    /**
     * 获取用户的权限
     */
    private static function getUserPris( string $uid) :? array
    {
        $pris = cache('user:'.md5($uid))['pri']['pris'];
        if(  $pris )
        {
            foreach( $pris as $v )
            {
                $pris[] = format_url($v);
            }
        }
        return $pris;
    }
}