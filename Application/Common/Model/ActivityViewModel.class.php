<?php
/**
 * Created by PhpStorm.
 * User: ssp
 * Date: 2016/8/18
 * Time: 9:19
 */

namespace Common\Model;
use Think\Model\ViewModel;

class ActivityViewModel extends ViewModel{

    public $viewFields = array(
        'Student_class' => array('student_id','class_id'),
        'Class_activity'=>array('class_id','activity_id'),
        'Activity'=>array('title','description','date','sender','_on'=>'Class_activity.activity_id=Activity.id'),
        'User' => array('name' => 'sender_name','_on'=>'Activity.sender=User.id'),
        'Class'=>array('_on'=>'Class_activity.class_id=Class.id','_on'=>'Student_class.class_id=Class.id')
    );
}