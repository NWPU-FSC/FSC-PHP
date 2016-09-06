<?php
namespace Common\Model;
use Think\Model\RelationModel;

class NewsModel extends RelationModel{


    protected $_link = array(
        'Sender' => array(
            'mapping_type' => self::BELONGS_TO,
            'class_name'   => 'User',
            'foreign_key'  => 'sender',
            'as_fields'    => 'name:sender_name'
        ),
        'NewsType' =>array(
            'mapping_type' => self::BELONGS_TO,
            'foreign_key'  => 'type',
            'class_name'   => 'NewsType',
            'as_fields'    => 'name:type_name',
       //     'condition'    => 'news_type.id < 128'
        ),
        'NewsStatus'=>array(
            'mapping_type' => self::BELONGS_TO,
            'foreign_key'  => 'status',
            'class_name'   => 'NewsStatus',
            'as_fields'    => 'name:status_name'
        ),
        'Class' =>array(
            'mapping_type' => self::MANY_TO_MANY,
            'foreign_key'  => 'activity_id',
            'relation_foreign_key' => 'class_id',
            'relation_table' => 'class_activity',
            'class_name' => 'Class'
        )
    );


    //判断是否为管理用


    //公用方法


    public function news_list($params){
        $where = ($this->relation(array('Sender','NewsStatus','NewsType'))->limit(get_limit($params))->order('is_stick=1 DESC,time DESC')->select());
        foreach($where as $key => $value){
            foreach($value as $key2 => $value2){
                if($key2 === 'type' && $value2 >= '128')
                    unset($where[$key]);
            }
        }
        $where = array_values($where);
        return $where;
    }

//    public function activity_list($params){
//        return $this->relation('Class')->limit(get_limit($params))->order('is_stick=1 DESC,time DESC')->select();
//    }

    // 管理用方法

    public function news_admin_add($params){
        $this->create($params);
        return $this->add();
    }

    public function news_delete($id){
        return $this->where("id=$id")->save(array('status'=>1));
    }

    public function news_remove($id){
        return $this->delete($id);
    }


}
