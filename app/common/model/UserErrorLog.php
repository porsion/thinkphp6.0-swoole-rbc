<?php 
namespace app\common\model;

class UserErrorLog extends \think\Model 
{
        protected $pk = 'auto_id';
        protected $table ="user_error_log";
        protected $updateTime = false;
        
        public function insData( \app\Request $req ) : \think\Model
        {
            $data = [
                'body' => json_encode( $req->param(), JSON_UNESCAPED_UNICODE),
                'uid'  => isset( $req->user_uid ) ? $req->user_uid : 0,
            ];
            $this->data($data);
            return $this;
        }
}