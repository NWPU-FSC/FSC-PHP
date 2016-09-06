<?php
/**
 * Created by PhpStorm.
 * User: zsy
 * Date: 2016/8/11
 * Time: 17:47
 */
namespace Admin\Controller;

use Admin\Controller\Base\BaseAdminController;
use Common\Model\DepartmentModel;
use Think\View;
use Think\Page;

class DepartmentController extends BaseAdminController
{

    public function index()
    {
        return $this->list();
    }

    public function list()
    {

        $this->set_title("部门列表");
        $this->_auth(C('LOGIN_ADMIN_LEVEL'));
        $department = D('department');
        $count = $department->where()->count();
        $perPage = 6;
        $page = new Page($count, $perPage);

        $data = $department->where()->limit($page->firstRow . ',' . $page->listRows)->select();
        $page_show = $page->show();
        $this->assign('page', $page_show);
        $this->assign('list', $data);
        $this->display('list');
    }

    public function editDe()
    {
        $this->set_title("编辑部门");
        $this->_auth(C('MOD_SCHOOL_MESSAGE_LEVEL'));

        $dId = I('get.id');
        $d = new DepartmentModel();
        $data = $d->where("id = $dId")->find();
        $this->assign('data', $data);

        $this->display();
    }

    public function saveDe()
    {
        $this->set_title("编辑部门");
        $this->_auth(C('MOD_SCHOOL_MESSAGE_LEVEL'));
        $d = D('department');
        $data = I('post.');
        $id = I('get.id');
        $flag = $d->where("id=$id")->save($data);
        if ($flag !== false)
            $this->success('修改成功', '/Admin/Department/list');
        else $this->error('修改失败');
    }

    public function assignDe(){
        $this->set_title("添加部门");
        $this->_auth(C('MOD_SCHOOL_MESSAGE_LEVEL'));
        $this->display();
    }

    public function add()
    {
        $this->_auth(C('MOD_SCHOOL_MESSAGE_LEVEL'));
        $d = D('department');
        $flag = false;

        if ($d->create()) {
            $flag = $d->add();
        }
        if ($flag !== false)
            $this->success('添加成功', '/Admin/Department/list');

        else $this->error('添加失败');
    }


    public function delete()
    {
        $this->set_title("删除部门");
        $this->_auth(C('MOD_SCHOOL_MESSAGE_LEVEL'));

        //$this->_auth(C('MOD_SCHOOL_MESSAGE_LEVEL'));
        $department = new DepartmentModel();
        $flag = $department->department_delete(I('get.id'));
        if ($flag !== false)
            $this->success('删除成功', '/Admin/Department/list');
        else $this->error('删除失败');
    }






//原编辑页面
//    public function edit(){
//        //这里进行两次处理 要先显示编辑页面
//        $de = new DepartmentModel();
//        //没post的情况
//        if(!IS_POST){
//            //进入编辑模式
//            $id=I('get.id/d');
//            if($id>0 && $data = $de->where("id=$id")->select()[0]) {
//                $this->assign('department', $data);
//                $this->display();
//            } else {
//                $this->error("获取失败 $id");
//            }
//        } else {
//            //处理编辑后的数据
//            $this->_auth(C('MOD_SCHOOL_MESSAGE_LEVEL'));
//            $de->create();
//            $this->result_json($de->field('name,description,location')->where('id='.I('get.id'))->save(),'编辑');
//        }
//    }
//
//    //api层级
//    //返回一个Markdown
//    public function markdown(){
//        $department= new DepartmentModel();
//        echo $department->find((int)I('get.id'))['description'];
//    }


}