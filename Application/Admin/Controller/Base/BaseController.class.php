<?php

namespace Admin\Controller\Base;
use Common\Controller\BaseApiController;


class BaseController extends BaseApiController{

    private $_user_access_level = null;


    protected function _auth($access_name=null)
    {

        if($this->_get_user_access_level()<$access_name?$access_name:C('DEFAULT_USER_LEVEL'))
            $this->error_json(\Common\Controller\ERROR_PERMISSION_DENIED);
    }

    protected function _get_user_access_level()
    {
        if($this->_user_access_level!=null) return $this->_user_access_level;

        $Model = D('User');

        if($user_id = session('user_id')) {
            $user = $Model->where("id=$user_id")->field('access')->limit(1)->select()[0];
            if ($user) {
                $this->_user_access_level = (int)$user['access'];
                return $this->_user_access_level;
            }
        }
        return 0;
    }


    protected function result_json($result,$error_code=null){
        if($result>0 || $result)
            $this->success_json(is_array($result)?$result:array('success'=>$result));
        else
            $this->error_json($error_code);
    }

    protected function result($result,$name=null)
    {
        $operate = $name?$name:'操作';
        if($result > 0)
            echo "{$operate}成功 ".I('get.id');
        else
            echo "{$operate}失败 ".I('get.id');
    }

}