<?php
namespace app\admin\controller;

use app\BaseController;
use app\common\model\Menu as M;
class Menu extends BaseController
{

    /**
     * 获取所有的菜单
     */
    public function index()
    {
        $data = M::select()->toArray();
        return $this->lay($data);
    }

    /**
     * 编辑某个菜单
     */
    public function edit()
    {
        $id = $this->request->get('id/d');
        if( !$id )
        {
            event('UserErrorLog',$this->request);
            return $this->error('参数错误！');
        }
        $data = M::find($id);
        if( $data->isEmpty() )
        {
            event('UserErrorLog',$this->request);
            return $this->error('参数错误！');
        }
        $all_data = M::select()->toArray();
        return $this->success( ['find_data' => $data->toArray() ,'all_data' => \create_menu( $all_data,'children' )]);
    }


    /**
     * 新增菜单
     */
    public function add()
    {
        $data = M::select()->toArray();
        return $this->success(\create_menu($data,'children'));
    }

    /**
     * 保存新增或修改后的菜单
     */
    public function save()
    {
        $data = $this->request->post('data');
        if( $data )
        {
            if(isset($data['auto_id']) && (int)$data['auto_id'] > 0)
            {
                $mode = M::find($data['auto_id']);
            }
            else
            {
                $mode = app(M::class);
            }
           $ret = $mode->save($data);
           if($ret)
           {
               return $this->success('菜单更新成功！');
           }
           return $this->error('菜单更新失败！');
        }
       return $this->error();
    }

    /**
     * 删除某个菜单
     */
    public function del()
    {
        $id = $this->request->get('id/d');
        $mode = M::find($id);
        if( !$mode->isEmpty() )
        {
            $ret = $mode->delete();
            if( $ret )
            {
                return $this->success('删除成功！');
            }
            return $this->error('删除失败，可能有用户组在使用该菜单！');
        }
        event('UserErrorLog',$this->request);
        return $this->error();

    }


}