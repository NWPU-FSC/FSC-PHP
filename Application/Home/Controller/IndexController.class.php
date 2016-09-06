<?php
namespace Home\Controller;
use Common\Model\MessageModel;
use Common\Model\MessageReceiverModel;
use Common\Model\NewsModel;
use Common\Model\UserModel;
use Think\Controller;
use Think\Model;

class IndexController extends Controller {
    public function index(){
        $news = new NewsModel();
        $data = $news->join('news_type ON news_type.id = news.type')
                         ->field('news.title,news_type.name,news.id')->select();
        $this->assign('list',$data);
        $this->display();
    }

    public function test(){
        $M = new MessageModel();
//        dump($M->get_message(1));
        $MR = new MessageReceiverModel();
        $params = array();
//        $params['sender'] = 1;
//
//        dump($M->get_message_list($params));
//
//        $params['sender'] = 2;
//        dump($M->get_message_list($params));

//        unset($params['sender']);
        $params['receiver'] = 83;
        dump($MR->get_receive_message_list($params));
//
//        $params['receiver'] = 11;
//        dump($M->get_receive_message_list($params));

//        $params['sender'] = 1;
//        dump($M->get_message_list($params));
//        $params['sender'] = 2;
//        dump($M->get_message_list($params));
    }

    public function test2(){

        $U = new UserModel();
        dump($U->award_list(85));

    }

    public function test3(){

        dump(match_password("123456"));
    }

}