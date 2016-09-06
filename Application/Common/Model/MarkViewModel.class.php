<?php
/**
 * Created by PhpStorm.
 * User: ssp
 * Date: 2016/8/18
 * Time: 9:19
 */

namespace Common\Model;
use Think\Model\ViewModel;

class MarkViewModel extends ViewModel{

    public $viewFields = array(
        'Mark' => array('student_id','course_id','exam_id','mark'),
        'User' => array('name' => 'student_name','_on'=>'Mark.student_id=User.id'),
        'Course' => array('name' => 'course_name','_on'=>'Mark.course_id=Course.id'),
        'Exam' => array('name' => 'exam_name','time','_on'=>'Mark.exam_id=Exam.id')
);
}