<?php
/**
 * Created by PhpStorm.
 * User: lol
 * Date: 2016/8/21
 * Time: 20:57
 */

namespace Common\Model;
use Think\Model\ViewModel;



class TeacherHwViewModel extends ViewModel
{
    public $viewFields = array(
        'Homework' => array('title','content','date','send_time','sender','id','_type'=>'LEFT'),
        'Course' => array('name' => 'course_name','id'=>'course_id', '_on'=>'Course.id=Homework.course_id','_type'=>'LEFT'),
        'HomeworkClass' => array('_on'=>'HomeworkClass.homework_id=Homework.id','_type'=>'LEFT'),
        'Class' => array('name' => 'class_name','id'=>'class_id','_on'=>'HomeworkClass.class_id=Class.id')
    );
}