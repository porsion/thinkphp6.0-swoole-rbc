<?php
use think\facade\Route;
Route::get('/admin/index','app\\admin\\controller\\Index@index');
Route::get('/admin/initmenu','app\\admin\\controller\\Index@initMenu')->ext('htm');
Route::get('/admin/initmsg','app\\admin\\controller\\Index@initMsg')->ext('htm');
Route::post('/admin/login','app\\admin\\controller\\Index@login')->ext('htm');
Route::get('/admin/logout','app\\admin\\controller\\Index@logout')->ext('htm');
Route::get('/admin/clear','app\\admin\\controller\\Index@clear')->ext('htm');

/**
 * 用户组管理
 */
Route::get('/admin/group','app\\admin\\controller\\Group@index')->ext('htm');
Route::get('/admin/group/add','app\\admin\\controller\\Group@add')->ext('htm'); // 新增或修改的view权限
Route::post('/admin/group/save','app\\admin\\controller\\Group@save')->ext('htm'); // 新增或修改的post权限
Route::rule('/admin/group/del','app\\admin\\controller\\Group@del','GET|POST')->ext('htm');
Route::get('/admin/group/menu','app\\admin\\controller\\Group@menu')->ext('htm'); // 用户组菜单设置view权限
Route::post('/admin/group/menu_save','app\\admin\\controller\\Group@menu_save')->ext('htm'); // 用户组菜单设置提交权限

/**
 * 针对某个用户组的所有操作
 */
Route::get('/admin/pri','app\\admin\\controller\\Pri@index')->ext('htm'); 
Route::get('/admin/pri/group_has_pri','app\\admin\\controller\\Pri@group_has_pri')->ext('htm'); // 获取某个组所有的权限
Route::get('/admin/pri/group_hasnt_pri','app\\admin\\controller\\Pri@group_hasnt_pri')->ext('htm'); // 获取某个组没有的权限
Route::post('/admin/pri/group_pri_add','app\\admin\\controller\\Pri@group_pri_add')->ext('htm'); // 为某个组加权限
Route::post('/admin/pri/group_pri_del','app\\admin\\controller\\Pri@group_pri_del')->ext('htm'); // 删除某个组的权限
Route::post('/admin/pri/save','app\\admin\\controller\\Pri@save')->ext('htm'); 
Route::rule('/admin/pri/del','app\\admin\\controller\\Pri@del','GET|POST')->ext('htm'); 
/**
 * 菜单操作
 */
Route::get('/admin/menu','app\\admin\\controller\\Menu@index')->ext('htm'); // 菜单首页
Route::get('/admin/menu/add','app\\admin\\controller\\Menu@add')->ext('htm'); // 菜单首页
Route::get('/admin/menu/edit','app\\admin\\controller\\Menu@edit')->ext('htm'); // 编辑某个菜单
Route::get('/admin/menu/del','app\\admin\\controller\\Menu@del')->ext('htm'); // 删除某个菜单
Route::post('/admin/menu/save','app\\admin\\controller\\Menu@save')->ext('htm'); // 编辑某个菜单

/**
 * 本站设置操作
 */
Route::get('/admin/set','app\\admin\\controller\\Setting@index')->ext('htm'); // 设置首页
Route::get('/admin/set/list','app\\admin\\controller\\Setting@list')->ext('htm'); // 设置首页
Route::post('/admin/set/save','app\\admin\\controller\\Setting@save')->ext('htm'); // 新增或修改设置项
Route::get('/admin/set/del','app\\admin\\controller\\Setting@del')->ext('htm'); // 删除某个设置项
Route::post('/admin/set/set_save','app\\admin\\controller\\Setting@set_save')->ext('htm'); // 更新配置值


/**
 * 用户管理类
 */
Route::get('/admin/user','app\\admin\\controller\\User@index')->ext('htm'); 
Route::post('/admin/user/insert','app\\admin\\controller\\User@insert')->ext('htm'); 
Route::get('/admin/user/del','app\\admin\\controller\\User@del')->ext('htm'); 


/**
 * 我的资料
 */
Route::post('/admin/center/save','app\\admin\\controller\\Center@save')->ext('htm'); 

/**
 * 日志类
 */
Route::get('/admin/log/login','app\\admin\\controller\\Log@Login')->ext('htm'); 
Route::get('/admin/log/err','app\\admin\\controller\\Log@err')->ext('htm'); 
Route::get('/admin/log/oplog','app\\admin\\controller\\Log@oplog')->ext('htm'); 