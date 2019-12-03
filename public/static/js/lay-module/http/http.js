layui.define(["jquery",'vCache','layer'], function (exports) {
    var    layer = layui.layer,
            vCache = layui.vCache,
            $ = layui.$;
    http = new function () {    
        this.get = function(url,data = {})
        {
                return http.ajax({type:'get',url:url,data:data})
        }
        this.post = function(url,data)
        {
            return http.ajax({type:'post',url:url,data:data})
        }

        this.getHtml = function (url,callback)
        {
            var v = new Date().getTime();
            var url = url.indexOf("?") > -1 ? url + '&v=' + v : url + '?v=' + v
            $.get(url,function(res){callback(res)});
        }
       
        this.ajax = function( options )
        {
            var load;window.layer_msg_box = null;
            options.dataType = options.dataType || 'json'
            options.beforeSend = options.beforeSend || function( xhr )
            {
                load = layer.msg('加载中...')
                var token = vCache.sGet('X-token')
                if( token )
                {
                   xhr.setRequestHeader('X-token', token)
                }
            }
            return $.ajax(options)
            .done(function (data) {
               if( data.code == 'success' )
               {
                   if(data.url)
                   {
                       setTimeout(() => {
                        window.location = data.url;
                       }, 1200); 
                   }
               }
               else if (data.code == 'need_login')
               {
                    vCache.sRm()
               }
               else 
               {
                   layer.msg(data.msg)
               }
            })
            .fail(function (err) {
                layer.alert('请求错误！')
            })
            .always(function (res) {
                layer.close(load);
                if(res.status === 200)
                {
                    var token = res.getResponseHeader('X-token') || res.token || '';
                    if( token )
                    {
                        vCache.sSet('X-token',token)
                    }
                    if( res.msg )
                    {
                        layer_msg_box = layer.msg(res.msg);
                    }
                }
            });
        }
    }

exports('http',http)
})