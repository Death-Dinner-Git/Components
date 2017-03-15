<?php
namespace app\admin\controller;
use think\Db;
use think\Cookie;
use think\Auth;
use think\Request;
use think\Model;

use think\Controller;

define('CONTROLLER_NAME',Request::instance()->controller());
define('MODULE_NAME',Request::instance()->module());
define('ACTION_NAME',Request::instance()->action());

class Common extends Controller
{
	public function _initialize()
    {
        /*$uid = Cookie::get('uid');
        if (empty($uid)) {
        	$this->error('请先登录',url('Login/login'));
        }*/

        $this->controller = CONTROLLER_NAME;
        $this->action = ACTION_NAME;


        $admin_id = $this->checklogin();
        if(!$admin_id){
            $this->error('未登录或登录已超时',url('Login/login'));
        }        
        $auth = new Auth;
        // 如果不是超级管理员，就检查权限并分配
        if($admin_id > 1){

            if(!$auth->check($this->controller.'/'.$this->action, $admin_id)){

                if(IS_POST){
                    $this->ajaxReturn(array('code'=>0,'message'=>'没有权限'));
                }else{
                    $this->error('没有权限');
                }
            }
        }
    }


    protected function checklogin()
    {
        $admin_id = Cookie::get('admin_id');
        if ($admin_id > 0) {
            return $admin_id;
        }
        $admin_model = model('Admin');
        $username = Cookie::get('se_admin_user');
        $info = $admin_model->getAdmininfo($username);
        if (Cookie::get('se_admin_key') != $admin_model->admin_key($info['id'], $info['token'])) {
            return false;
        } else {
            return $info['id'];
            unset($info);
        }
    }

}
