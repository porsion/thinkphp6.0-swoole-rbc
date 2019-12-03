

/**
 * 本地缓存工具
 */

layui.define([ "jquery"], function (exports) {

vCache = new function(){
    var $item;
    const SKEY = ''
    this.sGet = function(i) {
        if(!sessionStorage)
        {
            return false;
        }
        if(i.indexOf('.') !== -1)
        {
            var arr = String(i).split('.');
              $item = this.sGet(arr[0])
            return  $item ? ($item[arr[1]] ? $item[arr[1]] : false) : false;
        }
        return JSON.parse(sessionStorage.getItem( SKEY + i))
    }
    this.sSet = function(i,v){
        if(!sessionStorage)
        {
            return false;
        }
        if(i.indexOf('.') !== -1)
        {
            var arr = String(i).split('.');
            $item = this.sGet(arr[0]) || {}
            $item[arr[1]] = v;
            this.sSet(arr[0],  $item);
        }
        else
        {
            return sessionStorage.setItem( SKEY + i,JSON.stringify(v))
        }
    }
    this.sInc = function(i){
        
        this.sSet(i,this.sGet(i)*1 + 1*1)
    }
    this.sDec = function(i) 
    {
        let n = this.sGet(i);
        if(n > 0)
        {
            this.sSet(i,+n - 1)
        }
    }
    this.sRm = function (i = '') {
        if(!sessionStorage)
        {
            return false;
        }
        if(i.indexOf('.') !== -1)
        { 
           var arr = String(i).split('.');
           $item = this.sGet(arr[0]) || {}
           return arr[1] in $item && delete $item[arr[1]] && this.sSet(arr[0],$item)
        }
        else if(i)
        {
            return sessionStorage.removeItem( SKEY + i)
        }
        else
        {
            return sessionStorage.clear()
        }
            
    }
    this.lSet = function (i,v)  {
        if(!localStorage)
        {
            return false;
        }
        if(i.indexOf('.') !== -1)
        {
            var arr = String(i).split('.');
            $item = this.sGet(arr[0]) || {}
            $item[arr[1]] = v;
            this.lSet(arr[0],  $item);
        }
        else
        {
            return localStorage.setItem( SKEY + i,JSON.stringify(v))
        }
    }
    this.lGet = function(i)  { 
        if(!localStorage)
        {
            return false;
        }
        if(i.indexOf('.') !== -1)
        {
            var arr = String(i).split('.')
            $item = this.lGet(arr[0])
            return  $item ? ($item[arr[1]] ? $item[arr[1]] : false) : false;
        }
       return  JSON.parse(localStorage.getItem( SKEY +  i))
    }
    this.lRm =function (i = '') {
        if(!localStorage)
        {
            return false;
        }
        if(i.indexOf('.') !== -1)
        {
            var arr = String(i).split('.');
            $item = this.lGet(arr[0]) || {}
            return arr[1] in $item && delete $item[arr[1]] && this.lSet(arr[0],$item)
            //return ret;
        }
        else
        {
            if(i)
            {
                return localStorage.removeItem( SKEY + i)
            }
            else
            {
                return localStorage.clear();
            }
          
        }
    }
}
exports('vCache', vCache);
})
// export default vCache;