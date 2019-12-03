<?php 
namespace app\admin\controller;
use app\common\model\User as UserModel;
class User extends \app\BaseController
{


    /**
     * 列出所有后台用户
     */
    public function index()
    {

        $key = $this->request->get('key');
        $mode = app(UserModel::class);
        $where = [];
        if($key)
        {
            $where = function($query) use($key)
            {
                $query->where('phone|name',$key);
            };
            $rows = 20;
        }
        else
        {
            $rows = $mode->count();
        }
        $data = $mode->order('auto_id','desc')->where($where)->page($this->page,$this->limit)->select()->toArray();
       
        return $this->lay($data,$rows);
    }


    public function insert()
    {
        $data = $this->request->post('data');
        if( !$data['auto_id'] ) 
        {
            unset($data['auto_id']);
            $data['uid'] = md5(uniqid());
        }
        if( $data['password'] )
        {
            $data['password'] = password_hash($data['password'],PASSWORD_DEFAULT);
        }
        $ret = app(UserModel::class)->save($data);
        if( $ret )
        {
            return $this->success('新增用户成功！');
        }
        return $this->error('发生严重错误！');
        
            
    }


    public function del()
    {
        $u_auto_id = $this->request->get('id/d');
        if( $u_auto_id )
        {
            $ret = UserModel::find($u_auto_id);
            if( !$ret ) return $this->error('参数错误！');
            $ret->delete();
            return $this->success('删除成功！');
        }
        return $this->error();
    }



}