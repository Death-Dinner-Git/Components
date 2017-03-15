<?php

namespace app\admin\controller;
use think\View;
use think\Request;
use think\Cookie;
use think\Db;

class Admin extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $view = new View();

        $list = Db::name('admin')->select();
        $model = Db::name('auth_group');
        foreach($list as $k=>$v){
            $map['id'] = $v['id'];
            $list[$k]['group'] = $model->where($map)->value('title');
        }
        $view->assign('list',$list);
        $view->title = '管理员';
        return $view->fetch();
    }

    /**
     * 添加管理员
     * @return [type] [description]
     */
    public function addadmin()
    {
        $view = new View();
        if (Request::instance()->isPost()){
            $string = new \String\String();
            $data['username'] = input('username','','trim');
            $data['password'] = input('password','','trim');
            $data['status']   = input('status',0,'intval');
            $data['token']    = $string::randString(8,0,'');
            $data['addtime']  = time();
            $group_id         = input('group_id',0,'intval');

            if(empty($data['username']) || empty($data['password'])){
                $res = array('code'=>0,'message'=>'请填写完整');
                return($res);
            }

            if($group_id == 0){
                $res = array('code'=>0,'message'=>'请选择用户组');
                return($res);
            }

            $where['username'] = $data['username'];

            $result = Db::name('admin')->where($where)->find();
            if ($result) {
                $res = array('code'=>0,'message'=>'已存在帐号'.$data['username']);
                return($res);
            }
            $data['password'] = $this->admin_key($data['password'], $data['token']);
            $re = Db::name('admin')->insertGetId($data);

            if($re){
                $data_acc['uid'] = $re;
                $data_acc['group_id'] = $group_id;

                $return = Db::name('auth_group_access')->insert($data_acc);

                if($return){
                    $res = array('code'=>1,'message'=>'添加管理员成功');
                }else{
                    $res = array('code'=>1,'message'=>'添加管理员成功，分配用户组失败');
                }
            }else{
                $res = array('code'=>0,'message'=>'添加管理员失败');
            }
            return($res);
        }elseif (Request::instance()->isGet()) {
            $list = Db::name('auth_group')->select();            
            $view->assign('list',$list);
            $view->title = '添加管理员';
            return $view->fetch();
        }
    }

    private function admin_key($mid, $token)
    {
        if(is_int($mid)){
            return md5(base64_encode($mid . $token));
        }else{
            return md5($mid . base64_encode($token));
        }
    }
    /**
     * 编辑管理员
     * @return [type] [description]
     */
    public function editadmin()
    {
        $view = new View();
        $id = input('id');        
        $where = array('id'=>$id);
        $info = Db::name('admin')->where($where)->find();

        $map = array('uid'=>$id);
        $acc_info = Db::name('auth_group_access')->where($map)->find();

        if (Request::instance()->isPost()){
            $data['password'] = input('password','','trim');
            $data['status']   = input('status');
            $group_id         = input('group_id');
            if($group_id == 0){
                $res = array('code'=>0,'message'=>'请选择用户组');
                return($res);
            }
            if(empty($data['password'])){
                unset($data['password']);
            }else{
                $data['password'] = $this->admin_key($data['password'], $info['token']);
            }
            $map = array('id'=>$id);
            $result = Db::name('admin')->where($map)->update($data);
            if($result){
                $data_acc['group_id'] = $group_id;
                if($acc_info){
                    $where['uid'] = $id;
                    $re = Db::name('auth_group_access')->where($where)->update($data_acc);
                    var_dump($re);die;
                    if($re){
                        $res = array('code'=>1,'message'=>'修改管理员成功');
                    }else{
                        $res = array('code'=>1,'message'=>'修改管理员成功，分配用户组失败');
                    }
                }else{
                    $data_acc['uid'] = $id;
                    $re = Db::name('auth_group_access')->insert($data_acc);
                    if($re){
                        $res = array('code'=>1,'message'=>'修改管理员成功');
                    }else{
                        $res = array('code'=>1,'message'=>'修改管理员成功，分配用户组失败');
                    }
                }
            }else{
                $res = array('code'=>0,'message'=>'修改管理员失败');
            }
            return($res);
        }elseif (Request::instance()->isGet()) {
            if(!$info){
                $this->error('不存在该管理员');
            }
            if($acc_info){
                $info['group_id'] = $acc_info['group_id'];
            }

            $list = Db::name('auth_group')->select();
            $view->list = $list;
            $view->info = $info;
            $view->title = '编辑管理员';
            return $view->fetch();
        }
    }
    /**
     * 删除管理员
     * @return [type] [description]
     */
    public function deladmin(){
        $id = input('id');
        if($id > 0){
            $where['id'] = $id;
            $re = Db::name('admin')->where($where)->delete();
            if($re){
                $res = array('code'=>1,'message'=>'删除管理员成功');
            }else{
                $res = array('code'=>0,'message'=>'删除管理员失败');
            }
            return($res);
        }
    }

    /**
     * 用户组列表
     * @return [type] [description]
     */
    public function auth_group()
    {
        $view = new View();
        $list = Db::name('auth_group')->select();
        $view->list = $list;
        $view->title = '角色管理';
        return $view->fetch();
    }

    /**
     * 添加用户组
     * @return [type] [description]
     */
    public function group_add()
    {
        $view = new View();
        if (Request::instance()->isPost()){
            $data = input('post.');
            $data['addtime']= time();
            if(empty($data['title'])){
                $this->error('请输入用户组名称');
            }else{
                $where = array('title'=>$data['title']);
                if(Db::name('auth_group')->where($where)->find()){
                    $this->error('已经存在该用户组');
                }
            }
            $rules = $data['rules'];
            if(empty($rules)){
                $this->error('权限不能为空');
            }
            $data['rules'] = implode(',', $rules);
            unset($rules);
            $result = Db::name('auth_group')->insert($data);
            if($result){
                $this->success('添加成功');
            }else{
                $this->error('添加失败');
            }
        }elseif (Request::instance()->isGet()) {
            $where = array();
            $field = 'id,name,title';
            $order = 'id asc';
            $list  = Db::name('auth_rule_class')->select();

            foreach($list as $k=>$v){
                $where['classid'] = $v['classid'];
                $list[$k]['rule'] = Db::name('auth_rule')->field($field)->where($where)->order($order)->select();
            }
            
            $view->list = $list;
            $view->title = '添加角色';
            return $view->fetch();
        }
    }

    /**
     * 修改用户组
     * @return [type] [description]
     */
    public function group_edit()
    {
        $view = new View();
        $id = input('id');
        if (Request::instance()->isPost()){
            $where['id'] = $id;
            $data = input('post.');
            $rules = $data['rules'];

            if(empty($data['title'])){
                $this->error('请输入用户组名称');
            }else{
                $map = array('title'=>$data['title'],'id'=>array('neq',$id));
                $res = Db::name('auth_group')->where($map)->find();
                if($res){
                    $this->error('已经存在该用户组');
                }
            }
            if(empty($rules)){
                $this->error('权限不能为空');
            }
            $data['rules'] = implode(',', $rules);
            unset($rules);

            $result = Db::name('auth_group')->where($where)->update($data);
            if($result){
                $this->success('编辑成功');
            }else{
                $this->error('编辑失败');
            }
        }elseif (Request::instance()->isGet()) {
            $id = input('id');
            $map = array('id'=>$id);
            $info = Db::name('auth_group')->where($map)->find();
            $list  = Db::name('auth_rule_class')->select();
            foreach($list as $k=>$v){
                $where['classid'] = $v['classid'];                
                $list[$k]['rule'] = Db::name('auth_rule')->where($where)->select();
            }
            $view->list = $list;
            $view->info = $info;
            $view->title = '编辑角色';
            return $view->fetch();
        }
    }

    /**
     * 规则列表
     * @return [type] [description]
     */
    public function rulelist()
    {
        $view = new View();

        $list = Db::name('auth_rule')->select();
        foreach($list as $k=>$v){
            $map = array('classid'=>$v['classid']);
            $list[$k]['classname'] = Db::name('auth_rule_class')->where($map)->value('classname');
        }
        $view->assign('list',$list);
        $view->title = '规则管理';
        return $view->fetch();
        
    }

    /**
     * 添加规则
     * @return [type] [description]
     */
    public function ruleadd()
    {
        $view = new View();

        if (Request::instance()->isPost()){
            $data = input('post.');

            if(empty($data['name'])){
                $this->error('请输入规则名称');
            }
            if(empty($data['title'])){
                $this->error('请输入规则描述');
            }
            $map = array('name'=>$data['name']);

            $info = Db::name('auth_rule')->where($map)->find();
            if($info){
                $this->error('已存在该规则名称');
            }
            $data['addtime'] = time();
            $result = Db::name('auth_rule')->insert($data);
            if($result){
                $this->success('添加规则成功');
            }else{
                $this->error('添加规则失败');
            }

        }elseif (Request::instance()->isGet()) {
            $list = Db::name('auth_rule_class')->select();
            $view->list = $list;
            $view->title = '添加规则';
            return $view->fetch();
        }
    }
    /**
     * 规则修改
     * @return [type] [description]
     */
    public function rule_edit()
    {
        $id = input('id');
        $where['id'] = $id;
        $info = Db::name('auth_rule')->where($where)->find();
        if(!$info){
            $this->error('不存在该规则');
        }
        $view = new View();
        if (Request::instance()->isPost()){
            $data = input('post.');

            if(empty($data['name'])){
                $this->error('请输入规则名称');
            }
            if(empty($data['title'])){
                $this->error('请输入规则描述');
            }
            if($info['name'] != $data['name']){
                $map = array('name'=>$data['name']);
                $res = Db::name('auth_rule')->where($map)->find();
                if($res){
                    $this->error('已存在该规则名称');
                }
            }
            $result = Db::name('auth_rule')->where($where)->update($data);

            if($result){
                $this->success('修改规则成功');
            }else{
                $this->error('修改规则失败');
            }

        }elseif (Request::instance()->isGet()) {
            $list = Db::name('auth_rule_class')->select();
            $view->assign('list',$list);
            $view->assign('info',$info);
            $view->title = '修改规则';
            return $view->fetch();
        }
    }

    /**
     * 规则分类
     * @return [type] [description]
     */
    public function ruleclass()
    {
        $view = new View();       
        $id = input('get.id','0','intval');
        if($id > 0){
            $where['classid'] = $id;
            $info = Db::name('auth_rule_class')->where($where)->find();
            if($info){
                $view->assign('info',$info);
            }
        }
        
        $list = Db::name('auth_rule_class')->select();

        $view->assign('list',$list);

        $view->title = '规则分类';
        return $view->fetch();
        
    }

    /**
     * 添加规则分类
     * @return [type] [description]
     */
    public function ruleclass_add()
    {
        $view = new View();

        if (Request::instance()->isPost()){
            $data = input('post.');

            if(empty($data['classname'])){
                $this->error('请输入分类规则名称');
            }

            $map = array('classname'=>$data['classname']);

            $info = Db::name('auth_rule_class')->where($map)->find();
            if($info){
                $this->error('已存在该分类规则名称');
            }
            $data['addtime'] = time();
            $result = Db::name('auth_rule_class')->insert($data);
            if($result){
                $this->success('添加分类规则成功');
            }else{
                $this->error('添加分类规则失败');
            }

        }elseif (Request::instance()->isGet()) {
            $view->title = '添加分类规则';
            return $view->fetch();
        }
    }

    /**
     * 编辑分类规则
     * @return [type] [description]
     */
    public function ruleclass_edit()
    {
        $view = new View();
        if (Request::instance()->isPost()){
            $data = input('post.');

            if(empty($data['classname'])){
                $this->error('请输入规则分类名称');
            }
            $map = array('classname'=>$data['classname']);
            $res = Db::name('auth_rule_class')->where($map)->find();
            if($res){
                $this->error('已存在该规则分类名称');
            }

            $result = Db::name('auth_rule_class')->where($where)->update($data);

            if($result){
                $this->success('修改分类规则成功');
            }else{
                $this->error('修改分类规则失败');
            }

        }elseif (Request::instance()->isGet()) {
            $id = input('id');
            $where['classid'] = $id;
            $info = Db::name('auth_rule_class')->where($where)->find();
            if(!$info){
                $this->error('不存在该规则分类');
            }
            $view->assign('info',$info);
            $view->title = '修改分类规则';
            return $view->fetch();
        }
    }

    /**
     * 修改密码
     */
    public function editpwd()
    {
        $view = new View();
        if (Request::instance()->isPost()){
            $data = input('post.');
            if($result){
                $this->success('修改密码成功');
            }else{
                $this->error('修改密码失败');
            }

        }elseif (Request::instance()->isGet()) {
            $view->title = '修改密码';
            return $view->fetch();
        }
    }

    public function logout()
    {
        cookie('se_admin_user', null);
        cookie('se_admin_key', null);
        cookie('admin_id', null);
        $this->success('注销成功！', url('Login/login'));
    }
}
