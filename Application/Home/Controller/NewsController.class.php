<?php
namespace Home\Controller;
use Common\Controller\BaseApiController;
use Common\Model\NewsModel;
use Common\Model\ParentStudentModel;
use Think\Page;


class NewsController extends BaseApiController {


    public function index(){

        $this->list();
    }

    public function read()
    {
        $news = new NewsModel();

        if (I('get.id'))
            if($data = $news->news_detail(array(
                    'id' => (int)I('get.id'),
                    'status' => 1
                    )))
            $this->success_json($data);
        $this->error_json(\Common\Controller\ERROR_DATA_NOT_FOUNT);
    }

    public function list(){

        $news = new NewsModel();

        $perPage = 6;
        $params = array(
            'status' => 1
        );

        if(I('get.sender'))
            $params['sender'] = (int)I('get.sender');
        if(I('get.number'))
            $params['number'] = (int)I('get.number');
        if(I('get.type')){

            $count = $news->join('news_type ON news_type.id = news.type')->join('user ON user.id = news.sender')
                ->field('news.*,news_type.name,user.name')->where("news.type = {$params['type']}")->count();
            $page = new Page($count, $perPage);

            $params['type'] = (int)I('get.type');
            $data = $news->join('news_type ON news_type.id = news.type')->join('user ON user.id = news.sender')
                    ->field('news.*,news_type.name,user.name')->where("news.type = {$params['type']}")->limit($page->firstRow . ',' . $page->listRows)->select();
            $ttttt=D("NewsType")->join('news on news.type = news_type.id')->where("news_type.id = {$params['type']}")->find();
        }else {
            $count = $news->join('news_type ON news_type.id = news.type')->join('user ON user.id = news.sender')
                ->field('news.*,news_type.name,user.name')->where("news.type = {$params['type']}")->count();
            $page = new Page($count, $perPage);


            $data = $news->join('news_type ON news_type.id = news.type')->join('user ON user.id = news.sender')
                    ->field('news.*,news_type.name,user.name')->where('news.type = 1')->limit($page->firstRow . ',' . $page->listRows)->select();
            $ttttt=D("NewsType")->join('news on news.type = news_type.id')->where("news_type.id = 1")->find();
        }
        foreach($data as $key => $value){
            $data[$key]['time'] = date("Y-m-d",strtotime($value['time']));
        }
        $type_list = D('NewsType')->select();
        $page_show = $page->show();

        $this->assign('type_list',$type_list);
        $this->assign('list',$data);
        //dump($data);
        $this->assign('type_id',$params['type']);
        $this->assign('ttt',$ttttt);
        $this->assign('page',$page_show);
        $this->display('list');
    }

    public function detail(){
        $news = new NewsModel();
        $params = (int)I('get.id');
        $data = $news->join('user ON user.id = news.sender')->where("news.id=$params")->field('title,content,name,time')->find();
        $type_list = D('NewsType')->select();
        $this->assign('type_list',$type_list);
        $this->assign('detail',$data);
        //dump($data);
        $this->display('detail');
    }


}

