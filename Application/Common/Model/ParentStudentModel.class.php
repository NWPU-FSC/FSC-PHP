<?php

namespace Common\Model;
use Think\Model\RelationModel;

/**
 * Created by PhpStorm.
 * User: Banixc
 * Date: 2016/8/14
 * Time: 8:39
 */


class ParentStudentModel extends RelationModel{

    protected $_link = array(
        'Parent'  =>  array(
            'mapping_type' => self::BELONGS_TO,
            'class_name' => 'User',
            'foreign_key' => 'parent_id',
            'as_fields' => 'name:parent_name'
        ),
        'Student'  =>  array(
            'mapping_type' => self::BELONGS_TO,
            'class_name' => 'User',
            'foreign_key' => 'student_id',
            'as_fields' => 'name:student_name'
        ),
        'RelationType'  => array(
            'mapping_type' => self::BELONGS_TO,
            'class_name' => 'RelationType',
            'as_fields' => 'name:relation_name',
            'foreign_key' => 'relation_id',
        )
    );



//    protected  $fields = array(
//        'news.id' => 'id',
//        'news.title' => 'title',
//        'news.type' => 'type',
//        'news.content' => 'content',
//        'news.sender' => 'sender',
//        'news.is_stick' => 'is_stick',
//        'news.time' => 'time',
//        'news.edit_time' => 'edit_time',
//        'news_type.name' => 'type_name',
//        'user.name' => 'sender_name'
//    );


    public function test(){
        dump($this->relation(true)->select()) ;
    }

    public function have_relation($parent_id, $student_id){
        $data = $this->where("parent_id = $parent_id AND student_id = $student_id")->select();
        return !empty($data);
    }

    public function get_relation_student($parent_id){
        return $this->where("parent_id = $parent_id")->relation(array('Student','RelationType'))->select();
    }



//    public function student_list($parent_id){
//        $this->where("parent_id = $parent_id")->select();
//    }



}