<?php
namespace Admin\Controller;
use Think\Controller;
use Admin\Controller\Base\BaseAdminController;
class HeaderController extends BaseAdminController{

    public function index(){
        $T = M('TeacherClassCourse');
        $params = session('user_id');
        $data = $T->join('class on teacher_class_course.class_id=class.id')->join('course on course.id=teacher_class_course.course_id')
                  ->field('class.id,class.name as class_name,teacher_class_course.course_id,course.name as course_name')
                  ->where("teacher_class_course.teacher_id=1")->select();
        // $this->success_json($data);
        //dump($data);
        $this->assign('tCourse',$data);
        $this->display('./header');
    }
}