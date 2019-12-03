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

    public function group()
    {
        $id = $this->request->get('id');
        if( $id )
        {
            $user_group_ids = \app\common\model\UserGroupMap::where('uid',$id)->column('group_id');
            $all_group = \app\common\model\UserGroup::select()->toArray();
            $new_group = [];
            $new_user_group_ids = [];
            if( $user_group_ids)
            {
                foreach($user_group_ids as $v)
                {
                    $new_user_group_ids[] = (string)$v;
                }
            }
            if( $all_group)
            {
                foreach($all_group as $k => $v)
                {
                    
                    $new_group[] = ['value' => (string)$v['auto_id'],
                                'title' => $v['name'],'disabled' => ''];
                }
            }
            return $this->success(['all_group' => $new_group,'user_group' => $new_user_group_ids]);
        }
        return $this->error();
       
   
    }

    public function changeGroup()
    {
        $id = $this->request->get('id');
        if( $id )
        {
            \app\common\model\UserGroupMap::where('uid',$id)->delete();
            if( $data = $this->request->post('data/a') )
            {
                foreach( $data as $v )
                {
                    $ins[] = ['uid' => $id,'group_id'=>$v,'join_time' => time()];
                }
                if( isset($ins) && count($ins) > 0)
                {
                    app(\app\common\model\UserGroupMap::class)->saveAll($ins);
                }
            }
            return $this->success('用户组修改成功！');
        }
        return $this->error();
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