<?php
namespace Common\Model;


use Think\Model\RelationModel;

class MessageReceiverModel extends RelationModel
{
    protected $_link = array(
        'Message' => array(
            'mapping_type' => self::BELONGS_TO,
            'class_name' => 'Message',
            'foreign_key' => 'message_id',
            'relation_foreign_key' => 'receiver',
            'as_fields'=>'id,content,sender,time,title,sender_name',
            'relation_deep'=>'Sender'
        ),
        'User' => array(
            'mapping_type' => self::BELONGS_TO,
            'class_name' => 'User',
            'foreign_key' => 'receiver',
            'as_fields'=>'name:receiver_name',
        )

    );


    public function get_receive_message_list($params){
        $limit = get_limit($params);
        $data = $this->relation('Message')->where("receiver = {$params['receiver']}")->order('message_id desc')->limit($limit)->select();
        foreach ($data as $k=>$value){
            if($value['has_read'])$data[$k]['has_read_name']='已读';
            else
                $data[$k]['has_read_name']='未读';
        }
        return $data;
    }

    public function get_receive_message_list_count($params){
        return $this->where("receiver = {$params['receiver']}")->count();

    }

    public function read_message($id,$receiver){
        $this->where("message_id = $id and receiver = $receiver")->setField('has_read',1);
    }

    public function get_receiver($id){
        return $this->relation('User')->where("message_id = $id")->select();
    }

}
