<?php
namespace app\admin\controller;
use app\BaseController;
use app\common\model\Config;
class Setting extends BaseController 
{
    /**
     * 获取所有的设置项
     */


    public function index()
    {

       $data = system_config(null);
        $index = []; $admin = [];
        foreach( $data as $k => $v )
        {
            if($v['type'] == 'admin')
            {
                $admin[] = $v;
            }
            else
            {
                $index[] = $v;
            }
        }
        return $this->success(compact('index','admin'));
    }

    /**
     * 以列表形式获取所有元数据
     */
    public function list()
    {
        $where = null;
        if( $key = $this->request->get('key') )
        {
            $where = function($query) use ($key)
            {
                $query->where('cn_name|k|desc','like',"%{$key}%");
            };
        }
        $data = Config::page($this->page,$this->limit)->where($where)->select()->toArray();
        return $this->lay($data,Config::count());
    }


    /**
     * 保存配置项
     */
    public function save()
    {
        $data = $this->request->post('data');
        if( !$data )
        {
            event('UserErrorLog',$this->request);
            return $this->error();
        }
        if( isset( $data['auto_id'] ) && (int)$data['auto_id'] > 0)
        {
            $info =  '更新';
            $mode = Config::where('auto_id',$data['auto_id'])->find();
        }
        else
        {
            $info = '新增';
            $mode = app(Config::class);
        }
        $ret = $mode->cache('config')->save($data);
        if( $ret )
        {
            return $this->success('配置项'.$info.'成功！');
        }
        return $this->error($info.'失败！唯一标识重复！');
    }

    /**
     * 删除配置项
     */
    public function del()
    {
        $k = $this->request->get('key');
        if(!$k)
        {
            event('UserErrorLog',$this->request);
            return $this->error();
        }
        $mode = Config::find($k);
        if( $mode->isEmpty() )
        {
            event('UserErrorLog',$this->request);
            return $this->error();
        }
        $ret = $mode->cache('config')->delete();
        if($ret)
        {
            return $this->success('删除成功！');
        }
        return $this->error();
    }

    /**
     * 更新配置值
     */
    public function set_save()
    {
        $data = $this->request->post('data');
        var_dump($data);
    }

}