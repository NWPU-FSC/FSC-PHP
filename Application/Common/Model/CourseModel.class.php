<?php
namespace Common\Model;
use Think\Model\RelationModel;

class CourseModel extends RelationModel{


    protected $_link = array(
        'Lecture' => array(
            'class_name' => 'User',
            'foreign_key' => 'course_id',
            'mapping_name' => 'lecture',
            'relation_table' => 'teacher_class_course',
            'mapping_type' => self::MANY_TO_MANY,
            'relation_foreign_key' => 'teacher_id',
        ),
    );

    public function get_course_list(){
        return $this->cache()->select();
    }

}
