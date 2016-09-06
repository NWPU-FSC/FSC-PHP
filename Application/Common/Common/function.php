<?php

function get_limit($params){
    $page = isset($params['page'])?$params['page']:0;
    $number = isset($params['numbers'])?$params['numbers']:C('PER_PAGE_NUMBER');
    return $page * $number.','. $number;
}
//根据参数判断是不传主键过来，若没有传id返回false
function get_params_id($params){
    if($params['id']>0 || $params){
        return true;
    }else return false;
}


//过滤参数
/**
 * @param $params array 被过滤的参数的数组 应当是一个关联数组
 * @param $list array 需要的参数列表 形如('a','b','c')
 * @return array 返回过滤后的$prarms
 */
function filter_params($params, $list){
    $filter_params = array();
    foreach ($list as $value)
        if(isset($params[$value]))
            $filter_params[$value]=$params[$value];
    return $filter_params;
}

function get_all_params($params,$array){
    $where = array();

    foreach ($array as $key => $value){
        if(isset($params[$key])){
            switch ($array[$key]){
                case 'int' :
                    $where[$key]=(int)$params[$key];
                    break;
                default:
                    $where[$key]=$params[$key];
            }
        }
    }
    return $where;
}
//用于产生随机字符串

function createNoncestr($length)
{
    $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
    $str ="";
    for ( $i = 0; $i < $length; $i++ )  {
        $str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);
    }
    return $str;
}

function createRandString($length){
    return bin2hex(random_bytes($length/2));
}

// 判断两人是否有关系
function have_relation($user_id1,$user_id2){
    //师生关系

    //学生家长关系

    //学生老师关系

    return true;
}

function get_receiver_list($receiver){
    return explode(";", $receiver);
}


function match_username($username){
    $pix='/^[a-zA-Z_]\w{5,17}$/';
    return(preg_match($pix,$username));
}


function match_password($password){
    $pix='/[-\da-zA-Z!@#$%^&*_]{5,21}$/';
    return(preg_match($pix,$password));
}