<?php 
namespace app\middleware\admin;
use app\common\EncodeId;
use think\facade\Cache;
class Login 
{

    use \app\middleware\traits\Auth;
    /**
     * 请求前的验证
     *
     * @param [type] $request
     * @param \Closure $next
     * @return void
     */
    public function handle($request, \Closure $next) 
    {
      
        $do_not_check_login_url   =  $this->getNotCheckLoginUrl();
        $baseUrl = format_url( $request->url() );
        
        $id = app(EncodeId::class)->decode( $request->header('X-token') );
        $uid_ttl = Cache::has('user:'.md5($id) );
        if(  !in_array( $baseUrl,$do_not_check_login_url ) && ( is_null($id)
        || !$uid_ttl ) )
        {
            return json(['code' => 'error','msg' => '未登录或已失效','url' => '/login.html']);
        }
        else if($id)
        {
              
            $ttl =  system_config('token_life_time');
            Cache::handler()->expire('user:'.md5($id),intval( $ttl ) * 60 );
            $request->enid = app(EncodeId::class)->encode($id,intval($ttl) * 60 );              
        }
      
        if( $id && $this->isAuthOpen() 
            && !$this->isInNotAuthUrl( $baseUrl )
            && $this->authUrlFail( $id, $baseUrl) 
            && !$this->isSikpRuleAuth($id) )
        {
            return json(['code' => 'error','msg' => '无权操作']);
        }
        $request->user_id = $id;
        return $next($request);
    }

    public function end( \think\Response $rep ) :void 
    {
        $req = app(\app\Request::class);
        $rep->header(['X-Token' => $req->enid]);
        
    }
}