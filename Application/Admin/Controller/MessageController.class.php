<?php
/**
 * Created by PhpStorm.
 * User: lol
 * Date: 2016/8/20
 * Time: 17:22
 */

namespace Admin\Controller;

use Admin\Controller\Base\BaseAdminController;
use Common\Model\MessageModel;
use Common\Model\MessageReceiverModel;
use Common\Model\PostModel;
use Common\Model\UserModel;
use Org\Markdown\Markdown;
class MessageController extends BaseAdminController
{

    public function index()
    {

        $this->receive();
    }

    public function receive()
    {
        $this->set_title("收件箱");

        $M = new MessageModel();
        $current = session('user_id');
        $page = I('get.page');
        if ($page > 0) $page--;
        $params = array(
            'receiver' => $current,
            'page' => $page
        );


        $this->count_page($page, $M->get_receive_message_list_count($params));
        $data = $M->get_receive_message_list($params);


        $this->assign('message_list', $data);
        $this->display();
    }

    public function send()
    {
        $this->set_title("发件箱");

        $M = new MessageModel();
        $current = $this->get_current_user_id();
        $page = I('get.page');
        if ($page > 0) $page--;
        $params = array(
            'sender' => $current,
            'page' => $page
        );

        $this->count_page($page, $M->get_send_message_list_count($params));
        $data = $M->get_send_message_list($params);
        $this->assign('message_list', $data);
        $this->display();

    }

    public function send_message()
    {
        $this->set_title("发送留言");

        if (!I('post.')) {
            $M = new UserModel();
            $user_list = $M->get_teacher_contacts($this->get_current_user_id());


            $this->assign('user_list',$user_list);


            $this->display();
        } else {

            $params = I('post.');

            $M = new MessageModel();


            $sender = $this->get_current_user_id();
            $content = $params['content'];
            $title = $params['title'];
            $receiver = $params['receiver'];
            $receiver = get_receiver_list($receiver);
            if(!$receiver[0])
                unset($receiver[0]);

            foreach ($params as $k => $v){
                if(strlen($k)>8 && substr($k,0,8) == 'user_id_' && $user_id = substr($k,8))
                    $receiver[] = $user_id;
            }

            $receiver = array_unique($receiver);

            $result = $M->send_message($sender, $title, $content, $receiver);
            if ($result) $this->success('发送成功', U('Message/send'));
            else $this->error('填写的用户ID有误');
        }

    }

    public function delete()
    {
        $this->set_title("删除留言");

        $id = I('get.id/d');
        $M = new MessageModel();
        $flag = $M->delete_message($id, $this->get_current_user_id());
        if ($flag)
            $this->success();
        else
            $this->error();

    }

    public function detail()
    {
        $this->set_title("留言详情");

        $id = I('get.id/d');
        $M = new MessageModel();
        if (!$M->have_access($id, $this->get_current_user_id())) {
            $this->error('没有权限！');
        } else {
            $data = $M->get_message($id);

            $MR = new MessageReceiverModel();
            $P = new PostModel();
            $receiver = $MR->get_receiver($id);
            $params = array();
            $params['message_id'] = $id;

//            $params['page'] = I('get.page/d');
//            if ($params['page'] == 0) $params['page'] = 1;
//            $params['page']--;
            $post = $P->get_message_post_list($params);
//            $all = $P->get_message_post_list_page($params);
//            $this->assign_page($params['page'], $all);
            $MR->read_message($id, $this->get_current_user_id());
            $data['content_markdown'] = Markdown::defaultTransform($data['content']);
            $this->assign('message', $data);
            $this->assign('post_list', $post);
            $this->assign('receiver_list', $receiver);

            $this->display();
        }

    }

    public function test()
    {
        $M = new MessageModel();

        dump($M->get_message(22));
    }

    public function post()
    {

        $params = I('post.');
//        dump($params);

        $M = new MessageModel();
        if (!$M->have_access($params['message_id'], $this->get_current_user_id())) {
            $this->error('没有权限！');
        } else {
            $P = new PostModel();
            if ($P->post_message($params['message_id'], $this->get_current_user_id(), $params['content']))
                $this->success();
            else
                $this->error();
        }
    }


}