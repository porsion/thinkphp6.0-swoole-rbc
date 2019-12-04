/**
 * date:2019/06/10
 * author:Mr.Chung
 * description:layuimini 框架扩展
 */

layui.define(["element", "jquery",'layer','http','vCache'], function (exports) {
    var element = layui.element,
        layer = layui.layer,
        $ = layui.$,
        vCache = layui.vCache,
        http = layui.http;
    layuimini = new function () {
        /**
         *  系统配置
         * @param name
         * @returns {{BgColorDefault: number, urlSuffixDefault: boolean}|*}
         */
        // this.config = function (name) {

        /**
         * 获取对应的id并返回一个数组
         */
        this.ids = function( data, type = 'auto_id' )
        {
            var ids = new Array();
            data.map(function(v){
                ids.push(v[type]);
            })
            return ids;
        }

        /**
         * 遍历对应的dom，给dom添加默认值 data
         * 要求：input的name必须和data.xxx是一样的
         */
        this.render = function($dom, data)
        {
            if( data == undefined || $dom == undefined)
            {
                return null;
            }
            $.each(data,function(v){
                $dom.find('[name="'+ v +'"][data-each]').val(data[v]);
                var radio_dom = $dom.find('[data-radio] > input[value="' + data[v] + '"]')
                if( radio_dom.length > 0 )
                {
                    radio_dom.attr('checked',true) 
                }
            })
        }

        /**
         * 初始化设备端
         */
        this.initDevice = function () {
            if (layuimini.checkMobile()) {
                $('.layuimini-tool i').attr('data-side-fold', 0);
                $('.layuimini-tool i').attr('class', 'fa fa-indent');
                $('.layui-layout-body').attr('class', 'layui-layout-body layuimini-mini');
            }
        };
            /**
             * 初始化页面标题，并自动展开菜单栏
             * @param href 链接地址
             * @param title 页面标题
             */
        this.initPageTitle = function (href, title) {
            window.pageHeader = (title == undefined || title == '' || title == null) ? [] : [title];
            $("[data-one-page]").each(function () {
                if ($(this).attr("data-one-page") == href) {
                    var addMenuClass = function ($element, type) {
                        if (type == 1) {
                            $element.addClass('layui-this');
                            if (title == undefined || title == '' || title == null) {
                                var thisPageTitle = $element.text();
                                thisPageTitle = thisPageTitle.replace(/\s+/g, "");
                                pageHeader.push(thisPageTitle);
                            }
                            if ($element.attr('class') != 'layui-nav-item layui-this') {
                                addMenuClass($element.parent().parent(), 2);
                            } else {
                                var moduleId = $element.parent().attr('id');
                                var moduleTile = $("#" + moduleId + "HeaderId").text();
                                moduleTile = moduleTile.replace(/\s+/g, "");
                                pageHeader.push(moduleTile);
                                $(".layui-header-menu li").attr('class', 'layui-nav-item');
                                $("#" + moduleId + "HeaderId").addClass("layui-this");
                                $(".layui-left-nav-tree").attr('class', 'layui-nav layui-nav-tree layui-hide');
                                $("#" + moduleId).attr('class', 'layui-nav layui-nav-tree layui-this');
                            }
                        } else {
                            $element.addClass('layui-nav-itemed');
                            var parentTitle = $element.text();
                            parentTitle = parentTitle.replace(/^\s+|\s+$/g, "").split('\n');
                            pageHeader.push(parentTitle[0]);
                            if ($element.attr('class') != 'layui-nav-item layui-nav-itemed') {
                                addMenuClass($element.parent().parent(), 2);
                            } else {
                                var moduleId = $element.parent().attr('id');
                                var moduleTile = $("#" + moduleId + "HeaderId").text();
                                moduleTile = moduleTile.replace(/\s+/g, "");
                                pageHeader.push(moduleTile);
                                $(".layui-header-menu li").attr('class', 'layui-nav-item');
                                $("#" + moduleId + "HeaderId").addClass("layui-this");
                                $(".layui-left-nav-tree").attr('class', 'layui-nav layui-nav-tree layui-hide');
                                $("#" + moduleId).attr('class', 'layui-nav layui-nav-tree layui-this');
                            }
                        }
                    };
                    addMenuClass($(this).parent(), 1);
                    var pageHeaderHtml = '<a lay-href href="/">主页</a><span lay-separator="">/</span>\n';
                    for (var i = pageHeader.length - 1; i >= 0; i--) {
                        pageHeader[i] = pageHeader[i].replace(/\s+/g, "");
                        if (i != 0) {
                            pageHeaderHtml += '<a><cite>' + pageHeader[i] + '</cite></a><span lay-separator="">/</span>\n';
                        } else {
                            pageHeaderHtml += '<a><cite>' + pageHeader[i] + '</cite></a>\n';
                        }
                    }
                    $('.layuimini-page-header').removeClass('layui-hide');
                    $('#layuimini-page-header').empty().html(pageHeaderHtml);
                    return false;
                }
            });
        };

            /**
             * 初始化选项卡
             */
            this.initPage = function () {
                var locationHref = window.location.href;
                var urlArr = locationHref.split("#/");
                if (urlArr.length >= 2) {
                    var href = urlArr.pop();
                    layuimini.initConten(href);
                    layuimini.initPageTitle(href);
                }
            };

            /**
             * 初始化内容信息
             * @param container
             * @param href
             * @param isHash
             */
            this.initConten = function ( href = '') {
                
                if( href )
                {
                    var container = '.layuimini-content-page';
                    var v = new Date().getTime();
                    var url = href.indexOf("?") > -1 ? href + '&v=' + v : href + '?v=' + v
                    $(container).load(url,function(res,err,xhr){

                        if( xhr.status !== 200 )
                        {
                            if(xhr.status === 404 )
                            {
                                $(container).load('/page/404.html?v=' + v)
                            }
                            else
                            {
                                layuimini.msg_error('发生错误！没有可以加载的页面')
                            }
                            // layuimini.msg_error('发生错误！')
                             console.log(xhr.responseText)
                        }
                        else
                        {
                            layuimini.initPageTitle(href);
                        }
                    })
                }
            
            };

            /**
             * 初始化菜单栏
             * @param data
             */
            this.initMenu = function (data) {
                var headerMenuHtml = '',
                    headerMobileMenuHtml = '',
                    leftMenuHtml = '',
                    headerMenuCheckDefault = 'layui-this',
                    leftMenuCheckDefault = 'layui-this';
                    window.menuParameId = 1;
                $.each(data, function (key, val) {
                    headerMenuHtml += '<li class="layui-nav-item ' + headerMenuCheckDefault + '" id="' + key + 'HeaderId" data-menu="' + key + '"> <a href="javascript:;"><i class="layui-icon ' + val.icon + '"></i> ' + val.title + '</a> </li>\n';
                    headerMobileMenuHtml += '<dd><a href="javascript:;" id="' + key + 'HeaderId" data-menu="' + key + '"><i class="layui-icon ' + val.icon + '"></i> ' + val.title + '</a></dd>\n';
                    leftMenuHtml += '<ul class="layui-nav layui-nav-tree layui-left-nav-tree ' + leftMenuCheckDefault + '" id="' + key + '">\n';
                    var menuList = val.child;
                    $.each(menuList, function (index, menu) {
                        leftMenuHtml += '<li class="layui-nav-item">\n';
                        if (menu.child != undefined && menu.child != []) {
                            leftMenuHtml += '<a href="javascript:;" class="layui-menu-tips" ><i class="layui-icon ' + menu.icon + '"></i><span class="layui-left-nav"> ' + menu.title + '</span> </a>';
                            var buildChildHtml = function (html, child, menuParameId) {
                                html += '<dl class="layui-nav-child">\n';
                                $.each(child, function (childIndex, childMenu) {
                                    html += '<dd>\n';
                                    if (childMenu.child != undefined && childMenu.child != []) {
                                        html += '<a href="javascript:;" class="layui-menu-tips" ><i class="layui-icon ' + childMenu.icon + '"></i><span class="layui-left-nav"> ' + childMenu.title + '</span></a>';
                                        html = buildChildHtml(html, childMenu.child, menuParameId);
                                    } else {
                                        html += '<a href="javascript:;" class="layui-menu-tips" data-type="tabAdd"  data-one-page-mpi="m-p-i-' + menuParameId + '" data-one-page="' + childMenu.href + '"><i class="layui-icon ' + childMenu.icon + '"></i><span class="layui-left-nav"> ' + childMenu.title + '</span></a>\n';
                                        menuParameId++;
                                        window.menuParameId = menuParameId;
                                    }
                                    html += '</dd>\n';
                                });
                                html += '</dl>\n';
                                return html;
                            };
                            leftMenuHtml = buildChildHtml(leftMenuHtml, menu.child, menuParameId);
                        } else {
                            leftMenuHtml += '<a href="javascript:;" class="layui-menu-tips"  data-type="tabAdd" data-one-page-mpi="m-p-i-' + menuParameId + '" data-one-page="' + menu.href + '"><i class="layui-icon ' + menu.icon + '"></i><span class="layui-left-nav"> ' + menu.title + '</span></a>\n';
                            menuParameId++;
                        }
                        leftMenuHtml += '</li>\n';
                    });
                    leftMenuHtml += '</ul>\n';
                    headerMenuCheckDefault = '';
                    leftMenuCheckDefault = 'layui-hide';
                });
                $('.layui-header-pc-menu').html(headerMenuHtml); //电脑
                $('.layui-header-mini-menu').html(headerMobileMenuHtml); //手机
                $('.layui-left-menu').html(leftMenuHtml);
                element.init();
            };

        /**
         * 判断是否为手机
         */
        this.checkMobile = function () {
            var ua = navigator.userAgent.toLocaleLowerCase();
            var pf = navigator.platform.toLocaleLowerCase();
            var isAndroid = (/android/i).test(ua) || ((/iPhone|iPod|iPad/i).test(ua) && (/linux/i).test(pf))
                || (/ucweb.*linux/i.test(ua));
            var isIOS = (/iPhone|iPod|iPad/i).test(ua) && !isAndroid;
            var isWinPhone = (/Windows Phone|ZuneWP7/i).test(ua);
            var clientWidth = document.documentElement.clientWidth;
            if (!isAndroid && !isIOS && !isWinPhone && clientWidth > 768) {
                return false;
            } else {
                return true;
            }
        };
        /**
         * 成功
         * @param title
         * @returns {*}
         */
        this.msg_success = function (title) {
            return layer.msg(title, {icon: 1, shade: this.shade, scrollbar: false, time: 2000, shadeClose: true});
        };

        /**
         * 失败
         * @param title
         * @returns {*}
         */
        this.msg_error = function (title) {
            return layer.msg(title, {icon: 2, shade: this.shade, scrollbar: false, time: 3000, shadeClose: true});
        };

        this.side_fold = function( i = 0 ,$dom)
        {
            var logo_dom = $('.layui-header .layui-logo > a');
            var title = logo_dom.find('h1').text();
         //  console.log(title)
            if (i == 1) { // 缩放
                logo_dom.append(title.substr(0,1)).css('font-size','25px');
                $dom.attr('data-side-fold', 0);
                $dom.attr('data-tip','展开')
                $('.layuimini-tool i').addClass('layui-icon-spread-left');
                $('.layui-layout-body').attr('class', 'layui-layout-body layuimini-mini');
            } else { // 正常
                logo_dom.html('<h1>'+title+'</h1>')
                $dom.attr('data-side-fold', 1);
                $dom.attr('data-tip','隐藏')
                $('.layuimini-tool i').removeClass('layui-icon-spread-left').addClass('layui-icon-shrink-right');
                $('.layui-layout-body').attr('class', 'layui-layout-body layuimini-all');
            }
            element.init();
        }

    };

        /**
         * 打开新窗口
         */
        $('body').on('click', '[data-one-page]', function () {
            var href = $(this).attr('data-one-page'),
                title = $(this).text();
            if( href && href != '0')
            {
                layuimini.initPageTitle(href, title);
                layuimini.initConten(href);
                layuimini.initDevice();
                vCache.sSet('load_url',href)

            }
            else
            {
                layuimini.msg_error('发生错误，没有可以加载的页面')
            }
            
        });
    /**
     * 左侧菜单的切换
     */
    $('body').on('click', '[data-menu]', function () {
        $parent = $(this).parent();
        menuId = $(this).attr('data-menu');
        // header
        $(".layui-header-menu .layui-nav-item.layui-this").removeClass('layui-this');
        $(this).addClass('layui-this');
        // left
        $(".layui-left-menu .layui-nav.layui-nav-tree.layui-this").addClass('layui-hide');
        $(".layui-left-menu .layui-nav.layui-nav-tree.layui-this.layui-hide").removeClass('layui-this');
        $("#" + menuId).removeClass('layui-hide');
        $("#" + menuId).addClass('layui-this');
    });

    /**
     * 菜单栏缩放
     */
    $('body').on('click', '[data-side-fold]', function () {
       
        var isShow = $(this).attr('data-side-fold');
        vCache.sSet('side_fold_is_show', +isShow )
        layuimini.side_fold( +isShow ,$(this));
       
      
    });

 

    /**
     * 监听提示信息
     */
    $("body").on("mouseenter", ".layui-menu-tips", function () {
        var tips = $(this).children('span').text(),
            isShow = $('.layuimini-tool i').attr('data-side-fold');
        if (isShow == 0) {
            openTips = layer.tips(tips, $(this), {tips: [2, '#2f4056'], time: 30000});
        }
    });
    $("body").on("mouseleave", ".layui-menu-tips", function () {
        var isShow = $('.layuimini-tool i').attr('data-side-fold');
        if (isShow == 0) {
            try {
                layer.close(openTips);
            } catch (e) {
                console.log(e.message);
            }
        }
    });

    exports("layuimini", layuimini);
});
