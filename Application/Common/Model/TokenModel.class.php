<?php
namespace Common\Model;
use Think\Model\RelationModel;

/**
 * Created by PhpStorm.
 * User: Banixc
 * Date: 2016/8/17
 * Time: 9:42
 */



class TokenModel extends RelationModel{

    const STUDENT_TYPE = 1;
    const PARENT_TYPE = 2;
    const TEACHER_TYPE = 3;


    protected $_link = array(
        'User'  =>  array(
            'mapping_type' => self::BELONGS_TO,
            'class_name' => 'User',
            'foreign_key' => 'user_id',
            'as_fields' => 'name:parent_name'
        ),
        'Student'  =>  array(
            'mapping_type' => self::BELONGS_TO,
            'class_name' => 'User',
            'foreign_key' => 'current_student',
            'as_fields' => 'name:student_name'
        ),
    );


    public function get_user_id($token){
        if(! $Token =  $this->where("token = '$token'")->find())
            return false;
        return $Token['user_id'];
    }

    public function get_current_student_id($token){
        if(! $Token =  $this->where("token = '$token'")->find())
            return false;
        switch ($Token['type']){
            case $this::STUDENT_TYPE:
                return $Token['user_id'];
            case $this::PARENT_TYPE:
                return $Token['current_student'];
            case $this::TEACHER_TYPE:
                return $Token['current_student'];
            default:
                return true;
        }
    }

    public function set_current_student($token,$student_id){
        $ParentStudent = new ParentStudentModel();
        if($ParentStudent->have_relation($this->get_user_id($token),$student_id)){
            return $this->where("token = '$token'")->setField('current_student',$student_id);
        }
        return false;
    }
}