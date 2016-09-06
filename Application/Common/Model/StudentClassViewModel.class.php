<?php
/**
 * Created by PhpStorm.
 * User: lol
 * Date: 2016/8/16
 * Time: 16:40
 */
namespace Common\Model;
use Think\Model\ViewModel;

class StudentClassViewModel extends ViewModel {
    public $viewFields = array(
        'StudentClass' => array('student_id'),
        'Class' => array('_on'=>'StudentClass.class_id=Class.id'),
        'HomeworkClass' =>array('_on'=>'HomeworkClass.class_id=Class.id'),
        'Homework'=>array('title','content','date','send_time','_on'=>'Homework.id=HomeworkClass.homework_id'),
        'User' =>array('name'=>'sender_teacher','_on'=>'Homework.sender= User.id'),
        'Course'=>array('name'=>'course_name','_on'=>'Course.id=Homework.course_id')
    );
}