<?php
/**
 * Created by PhpStorm.
 * User: lol
 * Date: 2016/8/12
 * Time: 17:13
 */
namespace Common\Model;

use Think\Model\RelationModel;

class DepartmentModel extends RelationModel{


//    protected $_link = array(
//        'Teacher' => array(
//            'class_name' => 'User',
//            'mapping_type' => self::MANY_TO_MANY,
//            'relation_table' => 'teacher_department',
//            'foreign_key' => 'department_id',
//            'relation_foreign_key' => 'teacher_id',
//            //'mapping_name' => 'deeee',
//            'mapping_fields' => 'name,access'
//        ),
//    );

    //管理方法
    public function department_admin_add($params){
        $this->create($params);
        return $this->add();
    }

    public function department_delete($id){
        return $this->where("id=$id")->delete();
    }

    public function department_teacher($params){
        return $this->join('teacher_department on department.id = teacher_department.department_id ')
                    ->join('LEFT JOIN user on user.id = teacher_department.teacher_id')
                    ->field('department.id as dId,department.name as dName,department.description,location,user.name as tName,user.phone,teacher_department.position')
                    ->where("department.id= $params")
                    ->select();
    }

    public function department_list(){
        return $this->select();
    }


}