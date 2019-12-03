<?php 
namespace app\common\model;


class LoginLog extends \think\Model 
{
        protected $pk = 'auto_id';
        protected $table ="user_login_log";
        protected $autoWriteTimestamp = 'create_time';
        protected $type = [
            'create_time'       => 'datetime',
        ];

        
        public function insData( \app\Request $req) : \think\Model
        {
            $ip2region = new \app\common\Ip2Region( app()->getConfigPath().'ip2region.db' );
            $data = [
                'body' => json_encode( $req->param(), JSON_UNESCAPED_UNICODE),
                'uid'  => isset( $req->user_uid ) ? $req->user_uid : 0,
                'ip'    => \ip2long( $req->ip()),
                'platform'  => \get_os( $req->server('HTTP_USER_AGENT')), //获取操作系统
                'location'  => $ip2region->memorySearch($req->ip())['region'] , //获取ip的地理位置
                'browser'   => \get_broswer( $req->server('HTTP_USER_AGENT') ), //获取客户端浏览器
            ];
            $this->data($data);
            return $this;
        }
}