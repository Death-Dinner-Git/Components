<?php
namespace app\admin\controller;
use think\View;
use think\Db;
use app\admin\model\Admin;

class Index extends Common
{

    public function index()
    {
    	$view = new View();

    	$view->title = '标题';

    	// $info = db('admin')->select();

    	// $view->assign('list',$info);



        $request = \think\Request::instance();
        $controller_name = $request->controller();
        $module_name = $request->module();
        $action_name = $request->action();
        return $view->fetch();
    }
}
