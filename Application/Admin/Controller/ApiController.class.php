<?php
namespace Admin\Controller;

use Admin\Controller\Base\BaseAdminController;
use Common\Model\UserModel;

/**
 * Created by PhpStorm.
 * User: Banixc
 * Date: 2016/8/22
 * Time: 16:45
 */

class ApiController extends BaseAdminController{


    public function news_type(){
        $N = M('NewsType');
        $data = $N->select();
        $this->success_json($data);
    }
    //老师能布置哪些作业
    public function teacher_class(){
        $T = M('TeacherClassCourse');
        $params = session('user_id');
        $data = $T->join('class on teacher_class_course.class_id=class.id')->join('course on course.id=teacher_class_course.course_id')
                  ->field('class.id as class_id,class.name as class_name,teacher_class_course.course_id as course_id,course.name as course_name')
                  ->where("teacher_class_course.teacher_id=$params")->group('course_id')->select();
        $this->success_json($data);
    }
    //老师管理的哪些班
    public function course_class(){
        $T = M('TeacherClassCourse');
        $paramsId = session('user_id');
        $course_id = I('post.key');
        //dump($course_id);
        $data1 = $T->join('class on teacher_class_course.class_id=class.id')->join('course on course.id=teacher_class_course.course_id')
            ->field('class.id as class_id,class.name as class_name,teacher_class_course.course_id,course.name as course_name')
            ->where("teacher_class_course.teacher_id=$paramsId AND course.id = $course_id")->select();
//        $this->success_json($data);

        $hw = D('TeacherHwView');
        $params['homework_id'] = I('post.id');
        $data = $hw->where("Homework.id = {$params['homework_id']}")->select();
        foreach ($data as $key => $value){
            foreach($data as $k2 =>$value1){
                if($data[$key]['id'] == $data[$k2]['id'] && $key!=$k2){
                    if(!is_array($data[$key]['class_id']))
                        $data[$key]['class_id'] = array($data[$key]['class_id'],$data[$k2]['class_id']);
                    else $data[$key]['class_id'][] = $data[$k2]['class_id'];
                    unset($data[$k2]);
                }
            }
        }
        $data2 = array();
        $data2[]=$data1;
        $data2[]=$data[0]["class_id"];
        $this->success_json($data2);
    }

    public function course_class1()
    {
        $T = M('TeacherClassCourse');
        $paramsId = session('user_id');
        $course_id = I('post.key');
        // dump($course_id);
        $data = $T->join('class on teacher_class_course.class_id=class.id')->join('course on course.id=teacher_class_course.course_id')
            ->field('class.id as class_id,class.name as class_name,teacher_class_course.course_id,course.name as course_name')
            ->where("teacher_class_course.teacher_id=$paramsId AND course.id = $course_id")->select();
        $this->success_json($data);
    }

    public function search_user(){
        $U = new UserModel();
        $this->success_json($U->search_user(I('get.name')));
    }
//不要删除 谢谢
//    public function sClass(){
//        $hw = D('TeacherHwView');
//        $params['homework_id'] = I('post.id');
//        $data = $hw->where("Homework.id = {$params['homework_id']}")->select();
//        foreach ($data as $key => $value){
//            foreach($data as $k2 =>$value){
//                if($data[$key]['id'] == $data[$k2]['id'] && $key!=$k2){
//                    if(!is_array($data[$key]['class_id']))
//                        $data[$key]['class_id'] = array($data[$key]['class_id'],$data[$k2]['class_id']);
//                    else $data[$key]['class_id'][] = $data[$k2]['class_id'];
//                    unset($data[$k2]);
//                }
//            }
//        }
//        $this->success_json($data[0]["class_id"]);
//    }


}