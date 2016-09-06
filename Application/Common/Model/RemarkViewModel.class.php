<?php
/**
 * Created by PhpStorm.
 * User: ssp
 * Date: 2016/8/18
 * Time: 9:19
 */

namespace Common\Model;
use Think\Model\ViewModel;

class RemarkViewModel extends ViewModel{

    public $viewFields = array(
        'Remark' => array('id','student_id','teacher_id','time','content'),
        'User' => array('name' => 'teacher_name','_on'=>'Remark.teacher_id=User.id')
    );
}