<?php
use Common\Model\UserModel;

/**
 * Created by PhpStorm.
 * User: Banixc
 * Date: 2016/8/16
 * Time: 10:28
 */

function get_token(){
    return createNoncestr(32);
}

//登陆时生成token
function get_token_hash($password,$token){
    return md5($password.$token.C('SECURE_KEY'));
}


function get_user_from_token($token){
    if(!$token) return false;
    $Token = D('Token');
    $data = $Token->cache(true)->where("token='$token'")->find();
    if(!$data) return false;
    $this->type = $data['type'];
    $User = new UserModel();
    return $User->get_user($token['user_id']);

}