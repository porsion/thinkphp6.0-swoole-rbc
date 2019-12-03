<?php
declare (strict_types = 1);

namespace app;

use think\App;
use think\exception\ValidateException;
use think\Validate;

/**
 * 控制器基础类
 */
abstract class BaseController
{
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    //protected $middleware = [];

    /**
     * 页面的条数
     */
    protected $limit = 0;

    /**
     * 当前第几页
     */
    protected $page = 0;
    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $this->app->request;

        // 控制器初始化
        $this->initialize();
    }


    protected function success($msg = '', ? array $data = []){
            if( is_array($msg) )
            {
                $data = $msg;
                $msg = '操作成功！';
            }
            else if ( empty($msg))
            {
                $msg = '操作成功！';
            }
        return $this->ret('success',$msg,$data);

    }

    protected function error($msg = '') 
    {
        $msg = $msg ? $msg : '操作失败！';
        return $this->ret('error',$msg);
    }


    private function ret( $code = 'success', string $msg = null, array $data = []) 
    {
            return  json( array_merge(['code' => $code,'msg' => $msg],['data'=> $data]),200);
    }

    protected function lay( array $arr, ? int $rows = 0)
    {
        $re['code'] = 0;
        $re['data'] = $arr;
        if($rows && $rows >= 1)
        {
            $re['count'] = $rows;
        }
        return $re;
    }
    // 初始化
    protected function initialize()
    {
        $this->limit = $this->request->get('limit/d');
        $this->page = $this->request->get('page/d');
    }

}
