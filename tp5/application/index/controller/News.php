<?php
namespace app\index\controller;
use think\View;
class News
{
    public function read()
    {
    	$view = new View();
    	return $view->fetch();
    }
    public function edit()
    {
        return 'edit';
    }
}
