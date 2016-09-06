<?php

/**
 * Created by PhpStorm.
 * User: Banixc
 * Date: 2016/8/11
 * Time: 16:09
 */
namespace Common\Controller;

use Think\Controller;


const STATUS_SUCCESS = 200;
//未定义的错误
const ERROR_UNDEFINED = -10000;
//没有找到
const ERROR_DATA_NOT_FOUNT = -10001;
//没有找到这条新闻
const ERROR_NEWS_NOT_FOUNT = -10002;

//用户名错误
const ERROR_USERNAME_ERROR = -10003;
//密码错误
const ERROR_PASSWORD_ERROR = -10004;
//权限不足
const ERROR_PERMISSION_DENIED = -10100;

//添加数据失败
const ERROR_DATA_ADD_ERROR = -10003;

//JSON解析错误
const ERROR_PARSE_ERROR = -11000;
//无效的方法
const ERROR_INVALID_METHOD = -11100;
//无效的参数
const ERROR_INVALID_PARAMS = -11200;

const ERROR_PARENT_ADD_FAILED = -12001;
//token获取错误
const ERROR_TOKEN_ERROR = -12002;
//token存储失败
const ERROR_TOKEN_MEMORY_ERROR = -12003;

//关系不正确 没有权限操作

const ERROR_NULL_RELATION = -12004;

//没有设置当前管理的学生
const ERROR_NULL_CURRENT_STUDENT = -12005;

const ERROR_NULL_STUDENT_ID = -12006;

const ERROR_USERNAME_EXIST = -12007;

const ERROR_ROLE_ERROR = -12008;

class BaseApiController extends Controller
{
    protected static $error_list = array(
        ERROR_UNDEFINED => 'Undefined error!(未定义的错误，请谨慎使用)',
        ERROR_DATA_NOT_FOUNT => 'Data not find!(没有找到这个错误)',
        ERROR_PERMISSION_DENIED => 'Permission denied!(权限不足)',
        ERROR_DATA_ADD_ERROR => 'Add item error!(添加数据失败)',
        ERROR_PARSE_ERROR => 'Json parse error!(JSON解析错误，请检查请求Json格式是否存在问题)',
        ERROR_INVALID_METHOD => 'Method invalid!(没有这个方法或方法无效)',
        ERROR_INVALID_PARAMS => 'Params invalid!(缺少参数或参数无效)',
        ERROR_PARENT_ADD_FAILED => 'Add parent error!(家长添加失败)',
        ERROR_NEWS_NOT_FOUNT => 'News not found(没有找到这个新闻)',
        ERROR_TOKEN_ERROR => 'get token error(缺少Token或Token不正确，请输入新的Token)',
        ERROR_USERNAME_ERROR => 'username error(用户名格式不正确，长度6-16，只能是大小写字母数字和下划线并只能已字母和下划线开头)',
        ERROR_PASSWORD_ERROR => 'password error(密码格式不正确，长度6-20，只能是字母、数字、特殊字符!@#$%^&*_',
        ERROR_TOKEN_MEMORY_ERROR => 'memory token error(token没有保存，请重试)',
        ERROR_NULL_RELATION => 'Invalid relation(更新管理孩子的时候 孩子和家长没有任何关系)',
        ERROR_NULL_CURRENT_STUDENT => 'Null current student(作为家长的身份登录而没有设置当前管理的学生)',
        ERROR_NULL_STUDENT_ID => 'Null student id(作为老师的身份登录而没有student_id的参数)',
        ERROR_USERNAME_EXIST => 'Username is dupe(用户名已存在)',
        ERROR_ROLE_ERROR => 'Role error(选择的身份错误)'
    );


    protected function error_json($error_code)
    {
        $error = array();
        if (BaseApiController::$error_list[$error_code]) {
            $error['status'] = $error_code;
        } else {
            $error['status'] = ERROR_UNDEFINED;
        }
        $error['message'] = BaseApiController::$error_list[$error['status']];
        $this->echoJson($error);
    }

    protected function success_json($result)
    {
        //避免错误
        if (!is_array($result)) $result = array('message' => $result);
        $this->echoJson(array(
            'status' => STATUS_SUCCESS,
            'result' => $result
        ));
    }

    private function echoJson($out)
    {
        $this->ajaxReturn($out, 'json');
    }


}