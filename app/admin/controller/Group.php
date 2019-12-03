<?php
namespace app\admin\controller;

use app\BaseController;
use app\common\model\UserGroup;
use app\common\model\Menu;
use app\common\model\UserGroupMenu as GMenu;
class Group extends BaseController 
{
    /**
     * 列出所有用户组
     */
    public function index()
    {
        $data = app(UserGroup::class)::order('auto_id','desc')->page($this->page,$this->limit)->select();
        $rows = app(UserGroup::class)::count();
        return $this->lay($data->toArray(),$rows);
    }

    /**
     * 增加新用户组或修改用户
     */
    public function add()
    {
        
        if( $id = $this->request->get('id/d') )
        {
            $data = app(UserGroup::class)->find($id);
            if($data)  
            {
                return $this->success($data->toArray());
            }
            event('UserErrorLog',$this->request);
            return $this->error('数据不存在！');
        }
        return $this->error();
    }

    /**
     * 提交修改或新增的权限 用户组
     */
    public function save()
    {
        if( $this->request->isPost())
        {
            $data = $this->request->post('data');
            if(isset($data['read_only_login']))
            {
                $data['read_only_login'] = 'y';
            }
            if( isset($data['auto_id']) && (int) $data['auto_id'] > 0 )
            {
                $mode = app(UserGroup::class)->find($data['auto_id']);
                if( !$mode )
                {
                    event('UserErrorLog',$this->request);
                    return $this->error();
                }
                $ret = $mode->allowField(['name','login_url','read_only_login'])->save($data);
            }
            else
            {
                $ret = app(UserGroup::class)::create($data,['name','login_url','read_only_login']);
            }
            
            if($ret || $ret->auto_id > 1)
            { 
                return $this->success();
            }
            return $this->error();
        }
        return $this->error();
    }

    /**
     * 删除用户组
     */
    public function del()
    {
        $id = $this->request->get('id');
       
        $is = false;
        if( $id > 1)
        {
            $mode = app(UserGroup::class)->find($id);
            if( !$mode )
            {
                $is = true;
            }
            else
            {
                $ret = $mode->delete();
            }
            
        }
        else if( $id = $this->request->post('data') )
        {
            if( is_array($id) )
            {
                $ret = app(UserGroup::class)::destroy($id);
            }
            else
            {
                $is = true;
            }
        }
        else
        {
            return $this->error();
        }
        if( $is )
        {
            // 用户提交的数据不合法
            event('UserErrorLog',$this->request);
            return $this->error('发生错误！');
        }
        if( $ret > 0 )
        {
           return  $this->success('删除成功！');
        }
        else if ( !$ret )
        {
           return $this->error('该用户组下面还有用户，不能删除！');
        }
        return $this->error();
    }


    /**
     * 获取某个用户组的菜单
     */
    public function menu()
    {
        $id = $this->request->get('id/d');
        if($id < 1)
        {
            event('UserErrorLog',$this->request);
        }
        $data = Menu::select()->toArray();
        $group_menu_ids = GMenu::where('group_id',$id)->column('menu_id');
        $group_menu_ids = array_diff( $group_menu_ids, array_unique( array_filter( array_column($data,'pid') ) ) );
        $ret = ['all_menu' => \create_menu($data,'children'),'group_menu_ids' => $group_menu_ids];
        return $this->success($ret);
    }

    /**
     * 修改某个用户组的菜单
     */
    public function menu_save()
    {
       $id = $this->request->post('ids');
       $group_id = $this->request->post('group_id');
       if( !$id )
       {
           $ret = GMenu::where('group_id',$group_id)->delete();
           if($ret)
           {
               go(function(){
                   app(GMenu::class)::setOplogType('delete');
                   event('Oplog',app(GMenu::class));
               });
               return $this->success('修改菜单成功！');
           }
       }
       else if( is_array($id) )
       {
            $group_menu_ids = GMenu::where('group_id',$group_id)->column('menu_id');
            foreach($id as $k => $v)
            {
                $ids[$v] = (int) $v;
            }
            $insert_data = \array_diff( $ids , $group_menu_ids);
            $delete_data = \array_diff($group_menu_ids, $ids);
            if( $insert_data  )
            {
                foreach($insert_data as $v)
                {
                    $save_data[] = [ 'menu_id' => $v, 'group_id'=> $group_id ];
                }
                app(GMenu::class)->saveAll($save_data);
            }
            if( $delete_data )
            {
                $mode = app(GMenu::class);
                $ret = $mode::whereIn('menu_id',$delete_data)->where('group_id',$group_id)->delete();
                if( $ret )
                {
                    go(function() use ($mode) {
                        $mode::setOplogType('delete');
                        event('Oplog',$mode);
                    });
                }
            }
            return $this->success('修改菜单成功！');

       }
       else
       {
            go(function(){
                event('UserErrorLog',$this->request);
            });
           return $this->error();
       }

    }
}