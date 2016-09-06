<?php
namespace Admin\Controller;


use Admin\Controller\Base\BaseController;

class IndexController extends BaseController {

    public function index(){
        if(session('user_id'))
            $this->redirect('Message/receive', 0,0, '页面跳转中...');
        else
            $this->redirect('Login/index',0,0,'页面跳转中...');
    }




}