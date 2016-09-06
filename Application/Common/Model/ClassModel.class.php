<?php
/**
 * Created by PhpStorm.
 * User: Banixc
 * Date: 2016/8/15
 * Time: 9:18
 */

namespace Common\Model;

use Think\Model\RelationModel;

class ClassModel extends RelationModel{

    private $a = 444;

    protected $_link = array(
        'Homework' => array(
            'class_name' => 'Homework',
            'relation_table' => 'homework_class',
            'mapping_type' => self::MANY_TO_MANY,
            'foreign_key' => 'class_id',
            'relation_foreign_key' => 'homework_id',
            'mapping_order' => 'send_time DESC',
            //'condition' => "b.date = (string)data('Y-m-d')",
            'relation_deep' => array('Class', 'Course', 'Sender'),
            'mapping_fields'    =>'title,content,id,date,course_id,sender'
        ),
        'Student' => array(
            'mapping_name' => 'student',
            'mapping_type' => self::MANY_TO_MANY,
            'class_name' => 'User',
            'relation_table' => 'student_class',
            'foreign_key' => 'class_id',
            'relation_foreign_key' => 'student_id'
        ),
        'Leader' => array(
            'mapping_type' => self::BELONGS_TO,
            'as_fields' => 'name:leader_name',
            'class_name' => 'User',
            'foreign_key' => 'leader'
        )
    );

    public function add_mul_class($class){
        return $this->addAll($class);
    }



}