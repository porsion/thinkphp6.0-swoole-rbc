<?php
namespace app\admin\controller;
use app\BaseController;
use app\common\model\UserPrivileges as Pris;
use app\common\model\GroupPrivilegesMap as Map;
class Pri extends BaseController 
{
    /**
     * 无条件列出所有权限
     */
    public function index()
    {
        $where = $this->request->get('key/s');
        if( !$where )
        {
            $data = Pris::order('auto_id','desc')->page($this->page,$this->limit)->select()->toArray();
            $rows = Pris::count();
        }
        else
        {
            $data = Pris::order('auto_id','desc')->where('desc|url','like',"%{$where}%")->page($this->page,$this->limit)->select()->toArray();
            $rows = 0;
        }
        
        return $this->lay($data,$rows);
    }


    /**
     * 列出某个用户组所拥有的权限
     */
    public function group_has_pri()
    {
        $id = $this->request->get('id/d');
        if( $id < 1 )
        {
            return $this->error('参数错误！');
        }
        $data = Pris::alias('p')->leftJoin('group_privileges_map map','map.pri_id = p.auto_id')
                ->where('map.group_id',$id)->order('p.auto_id','desc')->field('p.*,map.auto_id as map_auto_id')
                ->page($this->page,$this->limit)->select();
        if( !$data->isEmpty() )
        {
            
            return $this->lay($data->toArray(),Map::where('group_id',$id)->count());
        }
        return $this->error('没有数据');
    }

    /**
     * 列出用户组没有的权限
     */
    public function group_hasnt_pri()
    {
        $id = $this->request->get('id/d');
        if( $id < 1 )
        {
            return $this->error('参数错误！');
        }
        $auto_ids = Map::where('group_id',$id)->column('pri_id');
        $data = Pris::whereNotIn('auto_id',$auto_ids)->order('auto_id','desc')->page($this->page,$this->limit)
                                ->select();
        if( !$data->isEmpty() )
        {
            return $this->lay($data->toArray(),Pris::whereNotIn('auto_id',$auto_ids)->count());
        }
        return $this->error('没有数据');

    }

    /**
     * 为用户组增加权限
     */
    public function group_pri_add()
    {
        $id = $this->request->post('id/a');
        $group_id = $this->request->post('group_id');
        if( !$id || !$group_id)
        {
            event('UserErrorLog',$this->request);
            return $this->error('参数错误！');
        }
        if( count($id) <= 1 )
        {
            $ret = Map::create(['pri_id' => $id[0],'group_id'=>$group_id ]);
            
        }
        else
        {
            foreach($id as $k => $v)
            {
                $save_data[] = ['pri_id' => $v,'group_id' => $group_id];
            }
            $ret = app(Map::class)->saveAll($save_data);
        }

        if($ret)
        {
            return $this->success('新增权限成功！');
        }
       // event('SysErrorLog',$this->request);
        return $this->error();
        
    }

    /**
     * 删除某个用户组的权限
     */
    public function group_pri_del()
    {
        $id = $this->request->post('id/a');
        if( !$id )
        {
            event('UserErrorLog',$this->request);
            return $this->error('参数错误！');
        }
        $ret = Map::destroy($id);
        if($ret)
        {
            return $this->success('删除权限成功！');
        }
        return $this->error();
        
    }


    public function save()
    {
        $data = $this->request->post('data');
        if( !$data )
        {
            event('UserErrorLog',$this->request);
            return $this->error();
        }
        if(isset($data['auto_id']) && (int)$data['auto_id'] > 0)
        {
            $type = '更新';
            $mode = Pris::find($data['auto_id']);
        }
        else
        {
            $type =  '新增';
            $mode = app(Pris::class);
        }
       $ret = $mode->save($data);
       if($ret)
        {
            return $this->success("权限{$type}成功！");
        }
        return $this->error("权限{$type}失败！URL重复");
    }


    public function del()
    {
        $trigeer = false;
        if( $this->request->isGet() )
        {
            $id = $this->request->get('id/d');
            if( !$id )
            {
                $trigeer = true;
            }
            else
            {
                $ret = Pris::find($id);
                if( $ret->isEmpty() )
                {
                    $trigeer = true;
                }
                else
                {
                    $res = $ret->delete();
                }
            }
        }
        else if ($this->request->isPost())
        {
            $id = $this->request->post('ids/a');
            $res = Pris::destroy($id);
        }
        else
        {
            $trigeer = true;
            
        }
        if($trigeer)
        {
            event('UserErrorLog',$this->request);
            return $this->error();

        }
        if($res) return $this->success('删除成功！');
        return $this->error('删除失败！');
       
    }
    


    private  function listPri( $where = null): ? array
    {
        
        // if( is_null($where) )
        // {
        //     return Pris::order('auto_id','desc')->page($page,$limit)->select()->toArray();
        // }
        // $data = Pris::where($where)->order('auto_id','desc')->page($this->page,$this->limit)->select();
        // if($data)
        // {
        //     return $data->toArray();
        // }
        // return [];
    }
}