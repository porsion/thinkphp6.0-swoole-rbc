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
        
        
            
    }



}