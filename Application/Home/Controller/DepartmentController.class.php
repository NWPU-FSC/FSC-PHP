<?php
/**
 * Created by PhpStorm.
 * User: lol
 * Date: 2016/8/17
 * Time: 11:46
 */
namespace Home\Controller;

use Common\Controller\BaseApiController;
use Common\Model\DepartmentModel;
use Think\Controller;

class DepartmentController extends BaseApiController
{

    public function index()
    {
        $this->department();
    }

    public function department()
    {
        $department = new DepartmentModel();
        if (I('get.id')) {
            $params = (int)I('get.id');
            $data = $department->where("id = $params")->find();
            $teacher = $department->department_teacher(I('get.id'));
            //dump($data);
        } else {
            $data = $department->find(1);
            $teacher = $department->department_teacher(1);

        }

        $list = $department->department_list();

        $de_list1 = D('department')->select();
        $de_list = array();
        foreach ($de_list1 as $key => $item) {
            $de_list[$item] = $item['id'];
        }
        if (I('get.id'))
            $this->assign('de_list', I('get.id'));
        else $this->assign('de_list', 1);

        $this->assign('list', $list);
        $this->assign('data', $data);
        $this->assign('teacher', $teacher);
        $this->display('department');
    }
}
