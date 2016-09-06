<?php
/**
 * Created by PhpStorm.
 * User: lol
 * Date: 2016/8/14
 * Time: 17:26
 */
namespace Common\Model;

use Think\Model\RelationModel;

class AwardModel extends RelationModel{

    protected $_link = array(
        'AwardType'=>array(
            'mapping_type'   => self::BELONGS_TO,
            'class_name'     => 'AwardType',
            'foreign_key'    => 'award_type',
            'as_fields'      => 'name:type_name'
        )
    );

    public function award_admin_add($params){
    $this->create($params);
    return $this->add();
}

    public function award_delete($id){
        $this->create();
        $this->where("id= $id")->find();
        return $this->delete();
    }

    public function award_list($params){
        $map['student_id'] = $params['id'];
        return $this->relation(true)
            ->where($map)->select();
    }

//    public function test(){
//        dump($this->relation(true)->select()) ;
//
//    }
}