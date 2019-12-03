<?php 
namespace app\common\model;
class UserGroupMap extends \think\Model 
{
    protected $pk = 'auto_id';
    protected $table ="user_group_map";
    protected $autoWriteTimestamp = 'join_time';
    protected $updateTime = false;
}