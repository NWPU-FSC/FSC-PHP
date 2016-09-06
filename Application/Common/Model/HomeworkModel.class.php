<?php
namespace Common\Model;

use Think\Model\RelationModel;
use Think\Page;


class HomeworkModel extends RelationModel{

    protected $_link = array(
        'Course' => array(
            'mapping_type' => self::BELONGS_TO,
            'as_fields' => 'name:course_name',
            'foreign_key' => 'course_id'
        ),
        'Class' => array(
            'mapping_type' => self::MANY_TO_MANY,
            'relation_table' =>'homework_class',
            'foreign_key' =>'homework_id',
            'relation_foreign_key' =>'class_id',
            'mapping_fields' => 'id as class_id,name as class_name'
        ),
        'Sender' => array(
            'mapping_type' => self::BELONGS_TO,
            'as_fields' => 'id:sender',
            'class_name' => 'User',
            'foreign_key' => 'sender',
        )

    );

    public function hw_list($params){

        $per_page = 6;
        $array = array();
        $count = $this->relation(true)->order('date Desc')->where("sender = $params")->count();
        $page = new Page($count,$per_page);

        $array['data']= $this->relation(true)->order('date Desc')->limit($page->firstRow . ',' . $page->listRows)->where("sender = $params")->select();
        $array['page_show'] = $page->show();
        return $array;
    }

    public function save_homework($id, $content, $title, $date, $class_list){
        $this->startTrans();
        $flag = $this->where("id = $id")->save(array(
            'content' => $content,
            'title' => $title,
            'date' => $date
        ));
        if($flag===false)return false;
        else {
            $HC = M('HomeworkClass');
            $HC->where("homework_id = $id")->delete();
            $data = array();
            foreach ($class_list as $value){
                $data[] = array(
                    'homework_id' => $id,
                    'class_id' => $value
                );
            }
            if($HC->addAll($data) === false){
                $this->rollback();
                return false;
            } else {
                $this->commit();
                return true;
            }

        }
    }

    public function addHw($content,$title,$date,$sender,$course_id,$class_list){
        $this->startTrans();
        $id = $this ->add(array(
            'content' => $content,
            'title' => $title,
            'date' => $date,
            'sender' => $sender,
            'course_id' => $course_id
        ));
        if($id===false) {$this->rollback(); return false;}
        else{
            $HC = M('HomeworkClass');
            $data = array();
            foreach ($class_list as $value){
                $data[] = array(
                    'homework_id' => $id,
                    'class_id' => $value
                );
            }
            //dump($data);
            if($HC->addAll($data) === false){
                $this->rollback();
                return false;
            } else {
                $this->commit();
                return true;
            }
        }
    }

}
