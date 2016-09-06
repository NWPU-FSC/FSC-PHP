<?php
namespace Common\Model;

use Think\Model\RelationModel;


class MessageModel extends RelationModel{
    protected $_link = array(
        'Receiver' => array(
            'mapping_type'   => self::MANY_TO_MANY,
            'mapping_name' => 'receiver',
            'class_name' => 'User',
            'relation_table' => 'message_receiver',
            'foreign_key' => 'message_id',
            'relation_foreign_key' => 'receiver',
            'mapping_fields' => 'id,name'
        ),
        'Sender' => array(
            'mapping_type' => self::BELONGS_TO,
            'class_name' => 'User',
            'foreign_key' => 'sender',
            'as_fields' => 'name:sender_name'
        )
    );



    public function get_message_list($params){
        if(isset($params['sender'])) return $this->get_send_message_list($params);
        return $this->get_receive_message_list($params);
    }

    public function get_send_message_list($params){
        return $this->order('id desc')->relation(true)->where("sender = {$params['sender']}")->limit(get_limit($params))->select();
    }

    public function get_send_message_list_count($params){
        return $this->where("sender = {$params['sender']}")->count();
    }

    public function get_receive_message_list($params){
        $MR = new MessageReceiverModel();
        return $MR->get_receive_message_list($params);
    }

    public function get_receive_message_list_count($params){
        $MR = new MessageReceiverModel();
        return $MR->get_receive_message_list_count($params);
    }





    public function send_message($sender,$title,$content,$receiver){

        $U = new UserModel();

        $this->startTrans();
        $id = $this->add(array(
            'sender' => $sender,
            'title' => $title,
            'content' => $content,
        ));
        if($id){
            $MR = new MessageReceiverModel();
            $data = array();
            if(!is_array($receiver)){
                $data['message_id'] = $id;
                $data['receiver'] = $receiver;
                $result = $MR->add($data);
            } else{
                foreach ($receiver as $value){
                    if(!$U->find($value)){
                        $this->rollback();
                        return false;
                    }
                    $data[] = array(
                        'message_id' => $id,
                        'receiver' => $value,
                    );
                }
                $result = $MR->addAll($data);
            }
            if($result) return $this->commit();
            else {
                $this->rollback();
                return false;
            }

        }
        return false;

    }






    public function get_message($message_id){
        return  $this->relation(true)->find($message_id);
    }

    public function have_access($message_id,$user_id){
        return $this->is_sender($message_id,$user_id) || $this->is_receiver($message_id,$user_id);
    }

    private function is_sender($message_id,$user_id){
        return $this->where("id = $message_id AND sender = $user_id")->find()?true:false;
    }

    private function is_receiver($message_id,$user_id){
        $MV = M('MessageReceiver');
        return $MV->where("message_id = $message_id AND receiver = $user_id")->find()?true:false;
    }

    public function delete_message($message_id,$user_id){
        if($this->is_sender($message_id,$user_id))
        return $this->delete($message_id);
        return false;
    }



}