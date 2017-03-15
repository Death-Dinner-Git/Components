<?php
/**
 * 模型类文件
 */
namespace app\admin\model;
use think\Model;
use think\Cookie;
use think\Db;

class Admin extends Model
{
	protected $pk = 'id';

	private function getM() {
        return Db::name('admin');
    }
	
	private function getL(){
        // return M('admin_login_log');
        return Db::name('admin_login_log');
    }
	
	private function getR(){
		// return M('auth_rule');
        return Db::name('auth_rule');
	}
	
	private function getG(){
        return Db::name('auth_group');
	}
	
	private function getA(){
		// return M('auth_group_access');
        return Db::name('auth_group_access');
	}

    /*
     * 用户组列表
     */
    public function getgrouplist($where=array()){
        return  $this->getG()->where($where)->select();
    }

	/*
	 * 获取管理员信息
	 * @param $admin  ID或者帐号
	 */
	public function getAdmininfo($admin,$except = true){
        if(empty($admin)){
            return false;
        }
        if(is_int($admin)){
            $where['id'] = $admin;
        }else{
            $where['username'] = $admin;
        }
		$field = $except ? 'password' : '*';
        return $this->getM()->field($field, $except)->where($where)->find();
    }
    /*
     * 管理列表
     */
    public function getAdminList($where,$field='*',$order='id desc'){
        $field  = $field='*' ? 'password' : $field;
        $except = $field='*' ? true : false;
        return $this->getM()->where($where)->field($field, $except)->paginate(10)->order($order)->select();
    }
    /*
     * 统计总条数
     */
    public function getadmincount($where){
        return $this->getM()->where($where)->count();
    }

	public function updateadmin($where, $data){
        if(empty($where)){
            return false;
        }		
        return $this->getM()->where($where)->insert($data);
    }


	public function login($username,$password)
	{
		$info = $this->getAdmininfo($username, false);
        if(empty($info)){
            $this->addloginlog($username,'',0,'帐号不存在');
            return false;
        }
        if($info['status'] != 1){
            $this->addloginlog($username,$info['id'],0,'账号已被禁用');
            return false;
        }
        if($this->admin_key($password, $info['token']) != $info['password']){
            $this->addloginlog($username,$info['id'],0,'密码错误');
            return false;
        }else{
            //登陆记录操作
			$where['id'] = $info['id'];
			$data = array(
				'last_login_time' => time(),
				'last_login_ip'   => get_client_ip()
			);
            $this->updateadmin($where, $data);
            $this->addloginlog($username,$info['id'],1,'登录成功');
            Cookie::set('se_admin_user',$info['username']);
            Cookie::set('admin_id',$info['id']);
            Cookie::set('se_admin_key', $this->admin_key($info['id'],$info['token']));
            return true;
        }

	}

	/*
	 * 记录登录日志
	 */
	private function addloginlog($username='',$admin_id='',$status=1, $note = '', $login_type=''){
        $data = array(
            'admin_id'  => $admin_id,
            'username'  => $username,
            'ip'        => get_client_ip(),
            'inputtime' => time(),
            'login_type'=> $login_type,
            'status'    => $status,
            'note'      => $note
        );
        return $this->getL()->insert($data);
    }

    public function admin_key($mid, $token)
    {
		if(is_int($mid)){
			return md5(base64_encode($mid . $token));
		}else{
			return md5($mid . base64_encode($token));
		}
    }

}