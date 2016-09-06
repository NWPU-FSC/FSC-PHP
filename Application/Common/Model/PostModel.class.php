<?php
namespace Common\Model;
use Think\Model\RelationModel;

class PostModel extends RelationModel{
    protected $_link = array(
        'Sender' => array(
            'mapping_type' => self::BELONGS_TO,
            'class_name' => 'User',
            'foreign_key' => 'sender',
            'as_fields' => 'name:sender_name'
        )
    );


    public function get_message_post_list($params){
        $message_id = $params['message_id'];
        return $this->relation(true)->where("message_id = $message_id")->limit(get_limit($params))->select();
    }

    public function get_message_post_list_page($params){
        $message_id = $params['message_id'];
        return $this->where("message_id = $message_id")->count();
    }

    public function post_message($message_id,$sender,$content){
        return $this->add(array(
            'message_id' => $message_id,
            'sender' => $sender,
            'content' => $content,
        ));
    }
}
