<?php
// 应用公共文件
use think\facade\Cache;
/**
 * 判断操作系统
 *
 * @param string $agent
 * @return string|null
 */
function get_os( string $agent = null) : ? string
{

        $os = '0';
        if( is_null($agent) || empty( $os) ) return $os;
        if (preg_match('/win/i', $agent) && strpos($agent, '95'))
        {
          $os = 'Windows 95';
        }
        else if (preg_match('/win 9x/i', $agent) && strpos($agent, '4.90'))
        {
          $os = 'Windows ME';
        }
        else if (preg_match('/win/i', $agent) && preg_match('/98/i', $agent))
        {
          $os = 'Windows 98';
        }
        else if (preg_match('/win/i', $agent) && preg_match('/nt 6.0/i', $agent))
        {
          $os = 'Windows Vista';
        }
        else if (preg_match('/win/i', $agent) && preg_match('/nt 6.1/i', $agent))
        {
          $os = 'Windows 7';
        }
    	  else if (preg_match('/win/i', $agent) && preg_match('/nt 6.2/i', $agent))
        {
          $os = 'Windows 8';
        }else if(preg_match('/win/i', $agent) && preg_match('/nt 10.0/i', $agent))
        {
          $os = 'Windows 10';
        }else if (preg_match('/win/i', $agent) && preg_match('/nt 5.1/i', $agent))
        {
          $os = 'Windows XP';
        }
        else if (preg_match('/win/i', $agent) && preg_match('/nt 5/i', $agent))
        {
          $os = 'Windows 2000';
        }
        else if (preg_match('/win/i', $agent) && preg_match('/nt/i', $agent))
        {
          $os = 'Windows NT';
        }
        else if (preg_match('/win/i', $agent) && preg_match('/32/i', $agent))
        {
          $os = 'Windows 32';
        }
        else if (preg_match('/linux/i', $agent))
        {
          $os = 'Linux';
        }
        else if (preg_match('/unix/i', $agent))
        {
          $os = 'Unix';
        }
        else if (preg_match('/sun/i', $agent) && preg_match('/os/i', $agent))
        {
          $os = 'SunOS';
        }
        else if (preg_match('/ibm/i', $agent) && preg_match('/os/i', $agent))
        {
          $os = 'IBM OS/2';
        }
        else if (preg_match('/Mac/i', $agent) && preg_match('/PC/i', $agent))
        {
          $os = 'Macintosh';
        }
        else if (preg_match('/PowerPC/i', $agent))
        {
          $os = 'PowerPC';
        }
        else if (preg_match('/AIX/i', $agent))
        {
          $os = 'AIX';
        }
        else if (preg_match('/HPUX/i', $agent))
        {
          $os = 'HPUX';
        }
        else if (preg_match('/NetBSD/i', $agent))
        {
          $os = 'NetBSD';
        }
        else if (preg_match('/BSD/i', $agent))
        {
          $os = 'BSD';
        }
        else if (preg_match('/OSF1/i', $agent))
        {
          $os = 'OSF1';
        }
        else if (preg_match('/IRIX/i', $agent))
        {
          $os = 'IRIX';
        }
        else if (preg_match('/FreeBSD/i', $agent))
        {
          $os = 'FreeBSD';
        }
        else if (preg_match('/teleport/i', $agent))
        {
          $os = 'teleport';
        }
        else if (preg_match('/flashget/i', $agent))
        {
          $os = 'flashget';
        }
        else if (preg_match('/webzip/i', $agent))
        {
          $os = 'webzip';
        }
        else if (preg_match('/offline/i', $agent))
        {
          $os = 'offline';
        }
        else
        {
          $os = '未知操作系统';
        }
        return $os;  
}

/**
 * 获取浏览器信息
 *
 * @return void
 */
function get_broswer(? string $sys = '0') : string
{
    if($sys == 0 || empty($sys)) return '0';
    if (stripos($sys, "Firefox/") > 0) {
        preg_match("/Firefox\/([^;)]+)+/i", $sys, $b);
        $exp[0] = "Firefox";
        $exp[1] = $b[1];  //获取火狐浏览器的版本号
    } elseif (stripos($sys, "Maxthon") > 0) {
        preg_match("/Maxthon\/([\d\.]+)/", $sys, $aoyou);
        $exp[0] = "傲游";
        $exp[1] = $aoyou[1];
    } elseif (stripos($sys, "MSIE") > 0) {
        preg_match("/MSIE\s+([^;)]+)+/i", $sys, $ie);
        $exp[0] = "IE";
        $exp[1] = $ie[1];  //获取IE的版本号
    } elseif (stripos($sys, "OPR") > 0) {
            preg_match("/OPR\/([\d\.]+)/", $sys, $opera);
        $exp[0] = "Opera";
        $exp[1] = $opera[1];  
    } elseif(stripos($sys, "Edge") > 0) {
        //win10 Edge浏览器 添加了chrome内核标记 在判断Chrome之前匹配
        preg_match("/Edge\/([\d\.]+)/", $sys, $Edge);
        $exp[0] = "Edge";
        $exp[1] = $Edge[1];
    } elseif (stripos($sys, "Chrome") > 0) {
            preg_match("/Chrome\/([\d\.]+)/", $sys, $google);
        $exp[0] = "Chrome";
        $exp[1] = $google[1];  //获取google chrome的版本号
    } elseif(stripos($sys,'rv:')>0 && stripos($sys,'Gecko')>0){
        preg_match("/rv:([\d\.]+)/", $sys, $IE);
            $exp[0] = "IE";
        $exp[1] = $IE[1];
    }else {
       $exp[0] = "未知浏览器";
       $exp[1] = ""; 
    }
    return $exp[0].'('.$exp[1].')';
}



/**
 * 创建一个带有child的无限分类菜单
 *
 * @param array $data
 * @return void
 */
function create_menu(array $data, ?string $child = 'child' ) 
{

    foreach($data as $v)
    {
          $items[$v['auto_id']] = $v;      
    }
    $tree = [];
    foreach($items as $k => $item)
    {
      $items[$k]['id'] = $k;
        if($item['pid'] > 0)
        {
            $items[$item['pid']][$child][] = &$items[$k];
        }
        else
        {
            $tree[] = &$items[$k];
        }
    }

    return $tree;
}


function get_group_id( $user_id ) :?array 
{
    $cache =  Cache::get('user:'.md5($user_id));
    return $cache['pri']['group_ids'];
}

/**
 * 获取系统配置
 */
function system_config( $k = null )
{
      $data = cache('config');
      if( is_null($k) )
      {
        return $data;
      }
      
      $fun = function( $ks )
      {
          if(strpos($ks,'|'))
          {
            return app(\app\common\model\Config::class)->getNotCheckLoginUrl($ks);
          }
          else if( is_numeric($ks) )
          {
            return (int) $ks;
          }
          return $ks;
      };
      $sData = array_column($data,'v','k');
      if( is_string($k) )
      {
          return $fun($sData[$k]);
      }
      else if( is_array($k) )
      {
        $ret = [];
        foreach($k as $v)
        {
          if( isset($sData[$v]) )
          {
              $ret[$v] = $fun($sData[$v]);
          }
          else 
          {
            continue;
          }
        }
        return $ret;
      }
}

/**
 * 格式化一下需要验证权限的url
 */
function format_url(string  $url ) : string 
{
  if( strpos( $url,'.' ) )
  {
          $url = \explode('.',$url)[0];
  }
  return \think\helper\Str::lower( trim( $url, '/') );
}

