<?php
namespace Admin\Controller;

use Admin\Controller\Base\BaseAdminController;
use Common\Model\NewsModel;
use Think\Page;

class NewsController extends BaseAdminController {

    //界面层

    public function index(){
        $this->list();
    }

    public function list(){
        $this->_auth(C('LOGIN_ADMIN_LEVEL'));
        $this->set_title('新闻列表');
        $notice = new NewsModel();
        //$teacher_id = session('user_id');
        $count = $notice->join('news_type ON news_type.id = news.type')
            ->field('news.title,news_type.name as typename,news.id,news.time,news.is_stick')
            ->count();
        $perPage = 6;
        $page = new Page($count,$perPage);
        $page_show = $page->show();
        $data = $notice->join('news_type ON news_type.id = news.type')->join('user ON user.id = news.sender')
            ->field('news.title,news_type.name as typename,news.id,news.time,news.is_stick,user.name')
            ->order('news.time DESC')->limit($page->firstRow.','.$page->listRows)->select();

        $this->assign('page',$page_show);
        $this->assign('list',$data);
        $this->display('list');

    }

    public function editNews(){
        $this->set_title('编辑新闻');
        $this->_auth(C('MOD_NEWS_LEVEL'));

        $notice = new NewsModel();
        //$teacher_id = session('user_id');
        if(I('get.id')) {
            $params['id'] = I('get.id');
            $data = $notice->join('news_type ON news_type.id = news.type')
                ->field('news.title,news_type.name as typename,news.id,news.content,news.type')
                ->where("news.id={$params['id']}")->find();
        }
        $this->assign('data',$data);
        //dump($data);
        $this->display();
    }

    public function saveNews(){

        $this->set_title('保存新闻');
        $this->_auth(C('MOD_NEWS_LEVEL'));

        $notice = new NewsModel();
        $id = I('get.');
        $data['title']=I('post.title');
        $data['content']=I('post.content');
        $data['type'] = I('post.type');
        $result = $notice->where("id={$id['id']}")->save($data);
        if($result !== false) {
            $this->success('修改成功','/Admin/News/list');
        }else{
            $this->error('修改失败');
        }
    }

    public function assignNews(){
        $this->_auth(C('ADD_NEWS_LEVEL'));
        $this->set_title('添加新闻');
        $this->display();
    }
    public function add(){

        $this->_auth(C('ADD_NEWS_LEVEL'));

        $notice = D('News');
        $notice->create();
        $data['sender'] = session('user_id');
        $data['type'] = I('post.type');
        $data['title'] = I('post.title');
        $data['content'] = I('post.content');
        $result = $notice->add($data);
        if($result !== false) {
            $this->success('发送成功','/Admin/News/list');
        }else{
            $this->error('发送失败');
        }
    }

    public function remove()        //永久删除
    {
        $this->set_title('删除新闻');
        $this->_auth(C('REMOVE_NEWS_LEVEL'));

        //$this->_auth(C('REMOVE_NEWS_LEVEL'));
        $news = new NewsModel();
        $result = $news->news_remove(I('get.id'));
        if($result !== false) {
            $this->success('删除成功','/Admin/News/list');
        }else{
            $this->error('删除失败');
        }
    }



}