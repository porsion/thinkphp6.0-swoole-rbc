<?php
namespace app\admin\controller;

use app\BaseController;
use app\common\model\UserErrorLog;
use app\common\model\User;
use app\common\model\LoginLog;
use think\facade\Validate;
//use app\common\model\Config as SysConfig;
use app\common\EncodeId;
use app\common\model\UserGroupMenu;
use think\facade\Cache;
use app\common\model\UserGroup;
class Index extends BaseController
{

    public function login()
    {    
        $phone = $this->request->post('data')['phone'];
        if( ! Validate::isMobile($phone) )
        {
            event('UserErrorLog',$this->request);
            return $this->error('手机号格式不正确！');
        }
        $pwd  = $this->request->post('data')['password'];
        $user_mode = app(User::class);
        $ret = $user_mode->where('phone',$phone)->find();
        if( !$ret || !$ret->checkPwd($pwd) )
        {
            return $this->error('用户名或密码错误！');
        }
        // 判断用户状态及后台状态
        if( (int) $ret->getData('status') > 0) 
        {
            return $this->error('该用户的状态异常!');
        }
        $config = system_config(['is_read_only','is_read_only_msg','token_life_time']);//app(SysConfig::class)->whereIn('k',)->column('v','k');
        // 如果后台是维护模式，还要判断当前用户所处的用户组是否设置了维护模式下可登录 user_group.read_only_login
        $cache_data = ['phone'=> $ret->phone,'name'=>$ret->name,'uid'=>$ret->uid ,'pri'=>$ret->pri()];
        $user_group = UserGroup::find(end($cache_data['pri']['group_ids']));
        if( $config['is_read_only'] == 'y')
        {
            if( $user_group->read_only_login == 'n' )
            {
                    $is = UserGroup::whereIn('auto_id',$cache_data['pri']['group_ids'])->where('read_only_login','y')->count();
                    if( !$is || $is <= 0 )
                    {
                        return $this->error($config['is_read_only_msg']);
                    }
            }
        }
        go(function() use( $ret,$cache_data ){
            Cache::set('user:'.md5($ret->uid),$cache_data);
            $this->request->user_uid = $ret->uid;
            app(LoginLog::class)->insData($this->request)->save();

        });
        $token = app(EncodeId::class)->encode( $cache_data['uid'], intval( $config['token_life_time'] ) *60 );
        unset($cache_data['pri'],$cache_data['uid']);
        return $this->success('登录成功！正在跳转',
                                array_merge( $cache_data,
                                            [
                                                'url' => $user_group->url,
                                                'token' => $token
                                             ]));
    }


    // 登录成功后跳转到首页，并获取该用户的所有菜单，别人发给他的且未读的消息以及未读公告、角色组申请变更回执

    /**
     * 初始化首页菜单
     *
     * @return void
     */
    public function initMenu()
    {
        
        $group_id = \get_group_id($this->request->user_id); 
        $data = app(UserGroupMenu::class)->alias('g')->whereIn('g.group_id',$group_id)->leftJoin('menu m','g.menu_id = m.auto_id')
                    ->field('m.*')->select();
        if( $data )
        {
            $data = $data->toArray();
        }
     
        return $this->success(\create_menu($data));
    }

    public function logout()
    {
        $ret = Cache::delete('user:'.md5($this->request->user_id));
        if($ret)
        {
            return $this->success();
        }
        return $this->error();
    }


    /**
     * 清清配置缓存
     */
    public function clear()
    {
        if( cache('config',null))
        {
            return $this->success('缓存清楚成功！');
        }
        return $this->error();
    }
   
}
