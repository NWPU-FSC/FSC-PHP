<?php
/**
 * Created by PhpStorm.
 * User: Banixc
 * Date: 2016/8/9
 * Time: 8:49
 */

namespace Admin\Controller;


use Admin\Controller\Base\BaseAdminController;

class UploadController extends BaseAdminController
{
    public function pic(){
    $upload = new \Think\Upload();// 实例化上传类
    $upload->maxSize   =     20971520 ;// 设置附件上传大小
    $upload->exts      =     array('jpg', 'jpeg', 'gif', 'png', 'bmp', 'webp');// 设置附件上传类型
    $upload->rootPath  =     'Public/Uploads/'; // 设置附件上传根目录
    $upload->autoSub = true;
    $upload->subName = array('date','Ymd');
    //$upload->savePath  =     array('date','Ymd'); // 设置附件上传（子）目录

    // 上传文件
    $upload->saveName = time().'_'.mt_rand();
    $info   =   $upload->upload();

    if(!$info['editormd-image-file']) {// 上传错误提示错误信息
        $success=0;
        $message=$upload->getError();
        $url=null;
    }else{// 上传成功
        $success=1;
        $message='上传成功！';
        $url='/'.$upload->rootPath.$info['editormd-image-file']['savepath'] . $info['editormd-image-file']['savename'];
    }

    echo json_encode(array(
        'success' => $success,
        'message' => $message,
        'url' => $url
    ));
}

}