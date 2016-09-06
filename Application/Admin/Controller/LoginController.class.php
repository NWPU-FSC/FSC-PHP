<?php

namespace Admin\Controller;
use Admin\Controller\Base\BaseController;
use Common\Model\UserModel;

class LoginController extends BaseController {


    public function index(){
        if($this->_get_user_access_level()==0) {
            $this->display('login');
        }
        else
            $this->error('您已登录，无需再次登录！');
    }

    public function login(){

        $User = new UserModel();
        $username = I('post.username/s');
        $password = I('post.password/s');


        if(!match_username($username))
                $this->error("用户名格式不正确，长度6-16，只能是大小写字母数字和下划线");

        if(!match_password($password))
                $this->error("密码格式不正确，长度6-20，只能是字母、数字、特殊字符!@#$%^&*_");


        if(!$user = $User->find_user_by_name($username)){
            $this->error('用户名不存在');
        }
        if($user['access']<C('DEFAULT_TEACHER_LEVEL')){
            $this->error('权限不足，无法登录');
        }

        if($User->user_login($username,$password)){

            session('user_id', $user['id']);
            $this->success('登录成功','/Admin/Index');
        } else
            $this->error('密码错误');
    }

    public function logout(){
        session('user_id',null);
        $this->success('登出成功，现在跳转至登录页面',C('USER_AUTH_GATEWAY'));
    }

}