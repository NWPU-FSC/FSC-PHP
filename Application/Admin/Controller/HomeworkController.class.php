<?php

namespace Admin\Controller;
use Common\Model\HomeworkModel;
use Admin\Controller\Base\BaseAdminController;
use Common\Model\TeacherHwView;
use Think\Page;

class HomeworkController extends BaseAdminController {

    public function index(){
        $this->list();
    }

    public function list2(){
        $this->set_title("作业列表");
        $this->_auth(C('LOGIN_ADMIN_LEVEL'));
        $hw = D('TeacherHwView');
        $params['sender'] = session('user_id');
        $params['page'] = I('get.page');
        $data = $hw->where("sender = {$params['sender']}")->order('date Desc')->select();
        foreach ($data as $key => $value){
            foreach($data as $k2 =>$value){
                if($data[$key]['id'] == $data[$k2]['id'] && $key!=$k2){
                    $data[$key]['class_name'] = $data[$key]['class_name']." / ".$data[$k2]['class_name'];
                    unset($data[$k2]);
                }
            }
        }
        $data = array_values($data);
        dump($data);
        $this->assign('hw',$data);
        $this->display('list_copy');
    }

    //为分页设计的
    public function list(){
        $this->set_title("作业列表");
        $this->_auth(C('LOGIN_ADMIN_LEVEL'));
        $h = new HomeworkModel();
        $params['sender'] = session('user_id');
        $data = $h->hw_list($params['sender']);

        $this->assign('page',$data['page_show']);
        $this->assign('hw',$data['data']);
        $this->display('list');
    }

    public function editHw(){
        $this->set_title("编辑作业");
        $this->_auth(C('DEFAULT_TEACHER_LEVEL'));
        $hw = D('TeacherHwView');
        $params['homework_id'] = I('get.id');
        //$params['class_id'] = I('get.class_id');
        $data = $hw->where("Homework.id = {$params['homework_id']}")->select();
        $this->assign('hw',$data[0]);
        $this->display();
    }

    public function saveHw(){
        $this->set_title("保存作业");
        $this->_auth(C('DEFAULT_TEACHER_LEVEL'));
        $H = new HomeworkModel();
        $id = I('get.id/d');
        $params = I('post.');
//        dump($id);
//        dump($params);
        $class_list = array();
        foreach ($params as $k => $v){
            if(strlen($k)>9 && substr($k,0,9) == 'class_id_' && $class_id = substr($k,9))
                $class_list[] = $class_id;
        }
        $content = $params['content'];
        $title = $params['title'];
        $date = $params['date'];
        $result = $H->save_homework($id,$content,$title,$date,$class_list);
        if($result !== false) {
            $this->success('修改成功','/Admin/Homework/list');
        }else{
            $this->error('修改失败');
        }
    }
    public function assignHw(){
        $this->set_title("布置作业");
        $this->_auth(C('DEFAULT_TEACHER_LEVEL'));
        $course_id = I('get.course_id');
        //dump($course_id);
        $this->assign('course_id',$course_id);
        $this->display();
    }

    public function add(){
        $this->set_title("添加作业");
        $this->_auth(C('DEFAULT_TEACHER_LEVEL'));
        $hw = new HomeworkModel();
        $course_id = I('get.course_id');
//        dump($course_id);
        $params = I('post.');
        $sender = session('user_id');
//        dump($id);
//        dump($params);
        $class_list = array();
        foreach ($params as $k => $v){
            if(strlen($k)>9 && substr($k,0,9) == 'class_id_' && $class_id = substr($k,9))
                $class_list[] = $class_id;
        }
        $content = $params['content'];
        $title = $params['title'];
        $d1 = $params['date'];
        $d2 = date('Y-m-d');
        if($d1==null) $date = $d2;
        else $date = $d2;

        $result = $hw->addHw($content,$title,$date,$sender,$course_id,$class_list);
        if($result !== false) {
            $this->success('发送成功','/Admin/Homework/list');
        }else{
            $this->error('发送失败');
        }
    }

    public function delete(){
        $this->set_title("删除作业");
        $this->_auth(C('DEFAULT_TEACHER_LEVEL'));
        $H = new HomeworkModel();
        $id = I('get.id/d');
        $result = $H->where("id=$id")->delete();
        if($result !== false) {
            $this->success('删除成功','/Admin/Homework/list');
        }else{
            $this->error('删除失败');
        }
    }
}