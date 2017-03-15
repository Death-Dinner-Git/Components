<?php
namespace app\admin\controller;

use think\Controller;
use think\View;
use think\Request;
use think\Validate;
use app\admin\model\Admin;

use think\Session;

class Login extends Controller
{
    public function login()
    {
    	if(request()->isPost()){

    		// 接收表单数据
    		$request = Request::instance();
    		$input = $request->post();

    		// 验证码检查
			if(!captcha_check($input['code'])) {
				$this->error('验证码不正确');
			}

			// 用户名验证和权限
            $db = new Admin;

            $res = $db->login($input['username'],$input['password']);

            if ($res) {
                $this->success('登录成功',Url('Index/index'));
            }else{
                $this->error('登录失败',Url('Login/login'));
            }            

    	}else{

	    	$view = new View();

	    	$view->title = '登录';

	        return $view->fetch();
    	}
    }

}
