layui.use(['jquery','http','form'],function(){
    var $ = layui.jquery,
        form = layui.form,
        http = layui.http,
        tipsBox;
    $('body').on('click','[data-common-add-click]',function(e){
        var url = $(this).attr('data-page');
        if( !url )
        {
            layer.alert('必须设置按钮的data-page属性为要load进来的页面，谢谢！');
            return 
        }
        var func = $(this).attr('data-callback');
        var h = $(this).attr('data-box-h');
        var w = $(this).attr('data-box-w')
        http.getHtml(url,function(res){
            w = w ? w : '600px';
            h = h ? h : 'auto' ;
            openBox = layer.open({
                type:1,
                area:[w,h],
                content:res,
                success:typeof window[func] ===  'function' && function(e){
                    window[func](e)
                }
            })
    })
    })
    form.on('submit(common-submit-save)',function(res){
        var data = res.field
            url = $(res.elem).attr('data-save-url');
        if( !url || url == undefined )
        {
            layer.msg('必须设置按钮的data-save-url属性为提交目标的地址')
            return ;
        }
        http.post(url,{data:data}).done(function(res){
            if(res.code == 'success')
            {
                setTimeout(() => {
                    layer.close(openBox)
                    tb.reload()
                }, 1200);
            }
            layer.msg(res.msg)
        })
    })


    $('body').on('click','.common-search',function(){
        var key = $('input[name="key"]').val();
        if( key )
        {
        var rows = 20;
            $("#common-search-tips").text('搜索时，结果里最多显示'+rows+'条信息')
            $(this).attr('data-search','yes');
            tb.reload({
                where:{key:key},
                limit:rows,
                page:false,
            })
        }
  })
    $('body').on('click','#common-clear-input',function(){
            var dom = $('input[name="key"]');
            dom.val('');
            var is = $('.common-search').attr('data-search');
            if( is == 'yes')
            {
                dom.trigger('propertychange')
            }
    })

    $('body').on('keydown','input[name="key"]',function(e){
        if (event.keyCode == "13") {
            $('.common-search').click();
        }
    })
    $('body').on('input propertychange','input[name="key"]' ,function(e){
        e.stopPropagation();
        var key =  $('input[name="key"]').val();
        var is = $('.common-search').attr('data-search');
        if( !key && is == 'yes')
        {
            $("#common-search-tips").text('')
            $('.common-search').attr('data-search','no');
            tb.reload({
                where:{key:''},
                limit:15,
                limits:[15,30,45,60],
                page:true
                
            });
        }
    })

    $('body').on('mouseenter','[data-tip]',function(){
        var text = $(this).attr('data-tip');
        tipsBox = layer.tips(text, $(this), {text: [2, '#2f4056'],tips:3});
    })
    $('body').on('mouseleave','[data-tip]',function(){
        layer.close(tipsBox)
    })

})