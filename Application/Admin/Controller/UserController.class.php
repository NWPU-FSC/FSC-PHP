<?php
namespace Admin\Controller;
use Admin\Controller\Base\BaseAdminController;
use Common\Model\UserModel;
use Org\Markdown\Markdown;

//用于管理用户的控制器

class UserController extends BaseAdminController {

	public function mod_password(){

        //TODO 请加上权限判断
        $this->_auth(C('MOD_USER_MESSAGE_LEVEL'));

        $User = new UserModel();

        $username = I('post.username/s');
        $password = I('post.password/s');

        if(!match_username(I('post.username')))
            $this->error('用户名格式不正确，长度6-16，只能是大小写字母数字和下划线');

        if(!match_password(I('post.password')))
            $this->error('密码格式不正确，长度6-20，只能是字母、数字、特殊字符!@#$%^&*_');

        if(!$user = $User->find_user_by_name($username)){
            $this->error('用户名不存在');
        }
        if($User->user_mod_password($username,$password)){
            $this->success('修改成功');
        } else {
            $this->error('修改失败');
        }

	}

	public function add_user(){
        $this->_auth(C('MOD_USER_MESSAGE_LEVEL'));

        $User = new UserModel();

        if(!match_username(I('post.username')))
            $this->error('用户名格式不正确，长度6-16，只能是大小写字母数字和下划线');

        if(!match_password(I('post.password')))
            $this->error('密码格式不正确，长度6-20，只能是字母、数字、特殊字符!@#$%^&*_');

        if($user = $User->find_user_by_name(I('post.username'))){
            $this->error('账号已存在');
        } else if ($User->add_user(I('post.'))) {
            $this->success('账号添加成功','../user');
        } else {
            $this->error('账号信息有误，无法添加');
        }
    }

    public function delete_user(){
        $this->_auth(C('MOD_USER_MESSAGE_LEVEL'));

        $User = new UserModel();

        if(empty(I('post.id'))){
            $this->error('id不能为空');
        }else if ($User->delete_user(I('post.'))) {
            $this->success('账号删除成功','../user');
        } else {
            $this->error('账号信息有误，无法删除');
        }
    }

	public function index(){
	    //这是管理用户的首页地址
        $this->_auth(C('MOD_USER_MESSAGE_LEVEL'));

        $this->display();
	}

    public function teacher_message(){

    }

	public function detail(){
        $u = new UserModel();
        $id = $this->get_current_user_id();
        $data = $u->teacher_message($id);
        $data['signature_markdown'] = Markdown::defaultTransform($data['signature']);
        $this->assign('teacher',$data);
        $this->display();

    }

    public function edit(){
        $u = D('User');
        $data = I('post.');
        $id = $this->get_current_user_id();

        $flag = $u->where("id=$id")->save($data);
        if ($flag !== false)
            $this->success('修改成功', '/Admin/User/detail');
        else $this->error('修改失败');
    }
}
?>