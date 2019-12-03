<?php
namespace app\admin\controller;

use app\BaseController;
use app\common\model\UserOplog;
use app\common\model\LoginLog;
use app\common\model\UserErrorLog as Err;
class Log extends BaseController
{

    public function login()
    {
        $data = LoginLog::page($this->page,$this->limit)->alias('log')->order('log.auto_id','desc')
                ->leftJoin('user u','u.uid = log.uid')
                ->field('u.phone,u.name,log.*')
                ->select()
                ->toArray();
        $rows = LoginLog::count();
        return $this->lay($data,$rows);
    }


    public function oplog()
    {
        $data = UserOplog::page($this->page,$this->limit)->alias('log')->order('log.auto_id','desc')
        ->leftJoin('user u','u.uid = log.uid')
        ->field('u.phone,u.name,log.*')
        ->select()
        ->toArray();
        $rows = UserOplog::count();
        return $this->lay($data,$rows);
    }

    public function err()
    {
        $data = Err::page($this->page,$this->limit)->alias('log')->order('log.auto_id','desc')
        ->leftJoin('user u','u.uid = log.uid')
        ->field('u.phone,u.name,log.*')
        ->select()
        ->toArray();
        $rows = Err::count();
        return $this->lay($data,$rows);
    }
}