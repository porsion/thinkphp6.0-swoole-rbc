<?php
namespace app\admin\controller;

use app\BaseController;
use app\common\model\User;
class Center extends BaseController 
{
    /**
     * 修改我的资料之后的保存
     */
    public function save()
    {
        $data = $this->request->post('data');
        if($data)
        {
            $mode = User::where('uid',$this->request->user_id)->find();
             if( $mode)
             {
                 if( isset($data['password']) && $data['password'])
                 {
                     $data['password'] = password_hash($data['password'],PASSWORD_DEFAULT);
                 }
                 $mode->data($data)->save();
                return $this->success('修改资料成功！');
             }
            return $this->error('发生严重错误！');    
        }
        return $this->error();

    }
}
