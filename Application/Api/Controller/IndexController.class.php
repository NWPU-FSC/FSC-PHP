<?php
namespace Api\Controller;

use Admin\Controller\Base\BaseController;
use Common\Model\MessageModel;
use Common\Model\NewsModel;
use Common\Model\ParentStudentModel;
use Common\Model\PostModel;
use Common\Model\TokenModel;
use Common\Model\UserModel;


const DEBUG = false;

const DEFAULT_TOKEN = "NWPU-FSC";
const DEFAULT_MESSAGE = "Hello world";

class IndexController extends BaseController
{


    private $unlogin_method = array(
        'register',
        'login',
        'news_list',
        'mod_password',
        'get_relation_list',
        'connect'

    );

    private $token;
    private $method;
    private $params;
    private $type;
    private $current_user;
    private $current_student_id;


    public function connect()
    {
        $this->result_json(array(
            'token' => DEFAULT_TOKEN,
            'message' => DEFAULT_MESSAGE
        ));
    }


    /**
     * API的方法从这里开始
     */
    // 新闻列表
    public function news_list()
    {
        $params = $this->filter_params(array(), array('page', 'status', 'type', 'sender'));

        $news = new NewsModel();

        $this->result_json($news->news_list($params), \Common\Controller\ERROR_DATA_NOT_FOUNT);

    }

    // 仅限家长的注册

    public function register()
    {
        $this->match_username_password();

        $params = $this->filter_params(array('name', 'username', 'password'));

        $parent = new UserModel();

        if ($parent->find_user_by_name($params['username']))
            $this->error_json(\Common\Controller\ERROR_USERNAME_EXIST);

        $params['access'] = 50;
        $user_id = $parent->add_user($params);
        if ($user_id) {
            $this->success_json(array(
                'id' => $user_id,
                'username' => $params['username'],
                'name' => $params['name']
            ));
        }
        $this->error_json(\Common\Controller\ERROR_PARENT_ADD_FAILED);
    }

    //用户登陆
    /**
     *
     */
    public function login()
    {
        $this->match_username_password();
        $params = $this->filter_params(array('username', 'password', 'type'));



        $User = new UserModel();
        if ($User->find_user_by_name($params['username'])) {
            if ($User->user_login($params['username'], $params['password'])) {

                $user = $User->find_user_by_name($params['username']);

                $data['token'] = createRandString(32);
                $data['user_id'] = $user['id'];
                $data['type'] = $params['type'];
                switch ($data['type']) {
                    case TokenModel::STUDENT_TYPE:
                        $flag = $user['access'] >= C('DEFAULT_STUDENT_LEVEL') && $user['access'] < C('DEFAULT_PARENT_LEVEL');
                        break;
                    case TokenModel::PARENT_TYPE:
                        $flag = $user['access'] >= C('DEFAULT_PARENT_LEVEL');
                        break;
                    case TokenModel::TEACHER_TYPE:
                        $flag = $user['access'] >= C('DEFAULT_TEACHER_LEVEL');
                        break;
                    default:
                        $flag = false;
                }
                if (!$flag) $this->error_json(\Common\Controller\ERROR_ROLE_ERROR);

                $token = D('Token');//登陆时将获取的token存入数据库

                if ($token->add($data)) {
                    $return = $User->get_user($data['user_id']);
                    $return = filter_params($return, array('username','name'));
                    $return['token'] = $data['token'];
                    $this->success_json($return);
                } else {
                    $this->error_json(\Common\Controller\ERROR_UNDEFINED);
                }

            } else {
                $this->error_json(\Common\Controller\ERROR_PASSWORD_ERROR);
            }
        } else {
            $this->error_json(\Common\Controller\ERROR_USERNAME_ERROR);
        }
    }

    //用户登出
    public function logout()
    {
        $Token = M('Token');
        $this->result_json($Token->where("user_id = {$this->current_user['id']} AND token = '$this->token'")->delete(), \Common\Controller\ERROR_UNDEFINED);
    }


    //根据学生id返回给客户端给客户端学生获奖情况
    public function award_list()
    {

        $student_id = $this->get_current_student();

        $award = new UserModel();
        $list = $award->award_list($student_id);
        if ($list)
            $this->success_json($list);
        $this->success_json(array());

    }

    //获取学生参加的考试列表
    public function get_exam_list()
    {
        $student_id = $this->get_current_student();
        $params['student_id'] = $student_id;
        $Model = D("MarkView");
        $this->result_json($Model->field('DISTINCT(exam_id),exam_name,time')->
        where("student_id = '{$params['student_id']}'")->order("time desc")->select()
            , \Common\Controller\ERROR_DATA_NOT_FOUNT);
    }

    //获取学生某次考试的全部成绩，传递参数exam_id
    public function get_mark()
    {
        $student_id = $this->get_current_student();

        $params = $this->filter_params(array('exam_id'));


        $params['student_id'] = $student_id;

        $Model = D("MarkView");

        $this->result_json($Model->field('student_name,exam_name,course_name,mark,time')->
        where("student_id = '{$params['student_id']}' and exam_id = '{$params['exam_id']}'")->select()
            , \Common\Controller\ERROR_DATA_NOT_FOUNT);
    }

    //查询家长的孩子
    public function parent_student()
    {
        //验证是家长的身份
        $this->oath_user_type(TokenModel::PARENT_TYPE);
        $parent_id = $this->current_user['id'];
        $student_id = $this->get_current_student(false);
        $relation = new ParentStudentModel();
        $data = $relation->relation(true)->where("parent_id = $parent_id")->select();
        foreach ($data as $key => $value) {

            if ($value['student_id'] == $student_id) {
//                unset($data[$key]);
                $data[$key]['is_current_student'] = true;
//                $data[] = $value;
            } else {
                $data[$key]['is_current_student'] = false;
            }
        }
        $this->result_json($data, \Common\Controller\ERROR_DATA_NOT_FOUNT);
    }

    //用于获取所有的亲戚关系 添加孩子的时候会用到
    public function get_relation_list()
    {
        $Relation = M('RelationType');
        $this->result_json($Relation->select(), \Common\Controller\ERROR_DATA_NOT_FOUNT);
    }

    //增加家长的孩子

    public function add_student()
    {
        $this->oath_user_type(TokenModel::PARENT_TYPE);

        $params = $this->filter_params(array('student_id', 'relation_id', 'student_name'));

        $params['parent_id'] = $this->current_user['id'];
        $U = M('User');
        $student = $U->find($params['student_id']);
        if ($student && $student['name']==$params['student_name']&&$student['access'] < C('DEFAULT_PARENT_LEVEL')) {
            $Relation = M('ParentStudent');
            $this->result_json($Relation->add($params), \Common\Controller\ERROR_UNDEFINED);
        } else {
            $this->error_json(\Common\Controller\ERROR_UNDEFINED);
        }

    }

    /**
     * 必选参数
     * student_id:学生ID
     * 默认参数
     * 返回后天到前天的作业按照时间的顺序排列
     *
     * 可选参数：
     * start:开始的日期
     * end:结束的日期
     * course_id:课程号
     */

    //获取作业时间列表
    public function get_homework_date_list(){
        $student_id = $this->get_current_student();
        $Model = D('HomeworkDateListView');

        $this->success_json($Model->field('DISTINCT(date)')->where("student_id = $student_id")->select());
    }
    //学生作业
    public function student_homework()
    {

        $student_id = $this->get_current_student();

        $params = $this->filter_params(array(),array('date'));

        $params['student_id'] = $student_id;

        $hw = D('StudentClassView');

        $d = date('Y-m-d',strtotime("+1 day"));
        $ds = date("Y-m-d",strtotime("-1 week"));;

        if (isset($params['date']))
            $this->success_json($hw->field('course_name,title,content,date,send_time,sender_teacher')->
            where("student_id='{$params['student_id']}' and Homework.date = '{$params['date']}'")->select());
        else $this->success_json($hw->field('course_name,title,content,date,send_time,sender_teacher')->
        where("student_id='{$params['student_id']}' and Homework.date < '$d' and Homework.date > '$ds'")->select());


    }

    //获取评价
    public function get_remark()
    {
        $student_id = $this->get_current_student();

        $params['student_id'] = $student_id;

        $Model = D("RemarkView");

        $this->result_json($Model->field('content,teacher_name,time')->
        where("student_id = '{$params['student_id']}'")->order("time desc")->select(), \Common\Controller\ERROR_DATA_NOT_FOUNT);
    }

    //考勤信息
    public function student_signin()
    {
        $student_id = $this->get_current_student();
        //$params = $this->filter_params(array('time'));
        $time2 = date('Y-m-d H:m:s');
        $time1 = date('Y-m-d H:m:s' , strtotime("-30 day"));
        $time1 = strtotime($time1);
        $time2 = strtotime($time2);
        $Model = D('signin');

        $this->success_json($Model->where("student_id=$student_id and time >$time1 and date_format(time,'%Y-%m-%d')<$time2")->order("time desc")->select());
    }

    //查看活动
    public function get_activity()
    {
        $student_id = $this->get_current_student();

        $Model = D('ActivityView');

        $this->success_json($Model->field('title,description,date,sender_name')->where("student_id=$student_id")->order("date desc")->select());
    }

    //更新家长管理的孩子
    public function update_current_student()
    {
        $this->oath_user_type(TokenModel::PARENT_TYPE);
        $params = $this->filter_params(array('student_id'));
        $Token = new TokenModel();
        if ($params['student_id'] == $this->get_current_student(false)) $this->success_json($this->get_current_user_id());
        $this->result_json($Token->set_current_student($this->token, $params['student_id']), \Common\Controller\ERROR_NULL_RELATION);

    }

    //查看班主任班级的学生家长
    public function teacher_parent()
    {
        $teacher_id = $this->current_user['id'];
        $Model = D('LeaderParentsView');
        $this->success_json($Model->
        field('parent_id,parent_name,parent_username,student_name,student_id,relation_status_name,status,teacher_id,relation_id,relation_type_name')->
        where("teacher_id = $teacher_id")->select());
    }

    //获取联系人列表,学生和家长返回所在班级的老师，老师返回班级所有学生的家长
    public function get_contacts()
    {

        switch ($this->type) {
            case TokenModel::STUDENT_TYPE:
            case TokenModel::PARENT_TYPE:
                $student_id = $this->get_current_student();
                $leader = D('StudentLeaderView');
                $array1 = $leader->field('id,name')->where("student_id = $student_id")->select();
                $teacher = D('StudentTeacherView');
                $array2 = $teacher->field('id,name')->where("student_id = $student_id")->select();

                $data = array_merge($array1, $array2);
                $Model = new UserModel();
                $data = $Model->array_unique_fb($data);
                $this->success_json($data);
                break;


            case TokenModel::TEACHER_TYPE:
                $teacher_id = $this->current_user['id'];
                $Leader = D('LeaderParentView');
                $array1 = $Leader->field('id,name')->where("teacher_id = $teacher_id")->select();
                $Teacher = D('TeacherParentView');
                $array2 = $Teacher->field('id,name')->where("teacher_id = $teacher_id")->select();

                $data = array_merge($array1, $array2);
                $Model = new UserModel();
                $data = $Model->array_unique_fb($data);
                $this->success_json($data);
                break;
            default:
                $this->error_json(\Common\Controller\ERROR_PERMISSION_DENIED);
        }
        return null;

    }
    // 假方法
    //学生的老师。。。。。。。。。。。。。。。。。。。。。。。
    public function student_teacher()
    {

        $student_id = $this->get_current_student();

        $ter1['id'] = 1;
        $ter1['name'] = '李';
        $ter1['course_id'] = 1;
        $ter1['course_name'] = '语文';

        $ter2['id'] = 6;
        $ter2['name'] = 'wang';
        $ter2['course_id'] = 0;
        $ter2['course_name'] = '班主任';

        $arr = array($ter1, $ter2);

        $this->success_json($arr);
    }

    //用户信息。。。。。。。。。。。。。。。。。。。。。。。。。
    public function user_detail()
    {
        $this->filter_params(array('user_id'));

        $user['name'] = 'zhangsan';
        $user['username'] = 'zhangsan';
        $user['gender'] = 'F';
        $user['email'] = '2789698789@qq.com';
        $user['phone'] = '46798609';
        $user['access'] = 300;
        $user['status'] = 1;
        $this->success_json($user);
    }

    //获取当前用户的message_list
    public function get_message_list()
    {
        $M = new MessageModel();
        if (isset($this->params['as_send']) && $this->params['as_send']) {//作为发送者的list
            $this->params['sender'] = $this->get_current_user_id();
        } else { //作为接收者的list
            $this->params['receiver'] = $this->get_current_user_id();
        }
        $this->success_json($M->get_message_list($this->params));

    }

    public function get_message_receiver()
    {
        $this->filter_params(array('message_id'));
        $M = new MessageModel();
        if (!$M->have_access($this->params['message_id'], $this->get_current_user_id()))
            //TODO
            $this->error_json(\Common\Controller\ERROR_UNDEFINED);
        $this->success_json($M->get_message($this->params['message_id'])['receiver']);
    }

    public function get_post_list()
    {
        $this->filter_params(array('message_id'));
        $M = new MessageModel();
        if (!$M->have_access($this->params['message_id'], $this->get_current_user_id()))
            //TODO
            $this->error_json(\Common\Controller\ERROR_UNDEFINED);
        $P = new PostModel();
        $this->success_json($P->get_message_post_list($this->params));
    }

    public function send_message()
    {
        $this->filter_params(array('title', 'content', 'receiver'));

        $user_id = $this->get_current_user_id();
        $receiver = $this->params['receiver'];
        if (!have_relation($user_id, $receiver))
            $this->error_json(\Common\Controller\ERROR_NULL_RELATION);
        $M = new MessageModel();
        $this->result_json($M->send_message($user_id, $this->params['title'], $this->params['content'], $this->params['receiver']), \Common\Controller\ERROR_UNDEFINED);
    }


    public function post_message()
    {
        $this->filter_params(array('message_id', 'content'));
        $M = new MessageModel();
        if (!$M->have_access($this->params['message_id'], $this->get_current_user_id()))
            //TODO
            $this->error_json(\Common\Controller\ERROR_UNDEFINED);
        $P = new PostModel();
        $this->result_json($P->post_message($this->params['message_id'], $this->get_current_user_id(), $this->params['content']), \Common\Controller\ERROR_UNDEFINED);
    }

    public function update_parent_student_status()
    {
        $this->oath_user_type(TokenModel::TEACHER_TYPE);
        $this->filter_params(array('parent_id', 'student_id', 'status'));
        $P = M('ParentStudent');
        $result = $P->save(array(
            'parent_id' => $this->params['parent_id'],
            'student_id' => $this->params['student_id'],
            'status' => $this->params['status'],
        ));
        $this->success_json($result);
    }


    //学生班级。。。。。。。。。。。。。。。。。。
    public function student_class()
    {

        $this->filter_params(array('student_id'));


        $class1['id'] = 1;
        $class1['name'] = '三年级一班';
        $class1['data'] = '2013-07-09';
        $class1['leader'] = 1;
        $class1['leader_name'] = '张三';


        $this->success_json($class1);
    }

    //老师所教班级的课程。。。。。。。。。。。。。。
    public function teacher_class_course()
    {
        $this->filter_params(array('teacher_id', 'class_id'));

        $course1['id'] = 1;
        $course1['name'] = '语文';


        $course2['id'] = 2;
        $course2['name'] = '数学';

        $data = array($course1, $course2);
        $this->success_json($data);
    }

    public function teacher_department()
    {
        $this->filter_params(array('teacher_id'));

        $td['id'] = 1;
        $td['name'] = '教务处';
        $td['description'] = '这里是学校的教务处，负责学生的各项工作';
        $td['loction'] = '学校办公大楼3层3011';

        $this->success_json($td);
    }

    /**
     * API的方法到这里结束
     */

    // API入口
    public function index()
    {
        $request = file_get_contents('php://input');

        $decoded_array = json_decode($request, TRUE);

        if (json_last_error())
            //出现错误抛出语法异常错误
            $this->error_json(\Common\Controller\ERROR_PARSE_ERROR);


        if (!isset($decoded_array['method']) || !method_exists($this, $decoded_array['method']))
            //抛出不可用请求错误
            $this->error_json(\Common\Controller\ERROR_INVALID_METHOD);
        else
            $this->method = $decoded_array['method'];


        if (!isset($decoded_array['params']))
            //抛出参数错误
            $this->error_json(\Common\Controller\ERROR_INVALID_PARAMS);
        else
            $this->params = $decoded_array['params'];

        if (isset($decoded_array['token']))
            $this->token = $decoded_array['token'];

        if (!in_array($this->method, $this->unlogin_method)) {
            //登录验证
            if (!$this->token || strlen($this->token) != 32) $this->error_json(\Common\Controller\ERROR_TOKEN_ERROR);
            //取得当前的用户
            $Token = D('Token');
            $data = $Token->where("token='$this->token'")->find();
            if (!$data) return false;
            $this->type = $data['type'];
            $User = new UserModel();
            $this->current_user = $User->get_user($data['user_id']);
            unset($this->current_user['password']);
            unset($this->current_user['secret']);

        }

        call_user_func(array($this, $this->method));
        return null;
    }

    /**
     * @param $require_params_list array 传入必选参数的列表
     * @param $optional_params_list array 传入可选参数的列表
     * @return array 过滤后的参数
     */

    private function filter_params($require_params_list, $optional_params_list = array())
    {
        foreach ($require_params_list as $value) {
            if (!array_key_exists($value, $this->params))
                $this->error_json(\Common\Controller\ERROR_INVALID_PARAMS);
        }
        $params_list = array_merge($require_params_list, $optional_params_list);

        return filter_params($this->params, $params_list);
    }


    // 权限验证 传入参数为 oath_user_type(STUDENT_TYPE) 代表仅有学生能访问 oath_user_type(STUDENT_TYPE, TEACHER_TYPE) 代表仅有学生和老师能访问

    private function oath_user_type()
    {
        if (!in_array($this->type, func_get_args()))
            $this->error_json(\Common\Controller\ERROR_PERMISSION_DENIED);
    }

    // 获取当前管理的学生，若失败则返回没有选择学生
    private function get_current_student($flag = true)
    {
        if ($this->current_student_id) return $this->current_student_id;
        switch ($this->type) {
            case TokenModel::STUDENT_TYPE:
                return $this->current_student_id = $this->current_user['id'];
            case TokenModel::PARENT_TYPE:
                $Token = new TokenModel();
                if ($id = $Token->get_current_student_id($this->token))
                    return $this->current_student_id = $id;
                else if ($flag)
                    $this->error_json(\Common\Controller\ERROR_NULL_CURRENT_STUDENT);
                else
                    return null;
                break;
            case TokenModel::TEACHER_TYPE:
                //TODO 需要添加一个函数判断老师和学生之间的关系
                $Token = new TokenModel();
                if ($id = $Token->get_current_student_id($this->token))
                    return $this->current_student_id = $id;
                if (isset($this->params['student_id']))
                    return $this->current_student_id = $this->params['student_id'];
                else
                    $this->error_json(\Common\Controller\ERROR_NULL_STUDENT_ID);
                break;
            default:
                $this->error_json(\Common\Controller\ERROR_PERMISSION_DENIED);
        }
        return null;
    }

    private function get_current_user_id()
    {
        return $this->current_user['id'];
    }


    private function match_username_password(){
        //验证用户名和密码
        $username = $this->params['username'];
            if(!match_username($username))
                $this->error_json(\Common\Controller\ERROR_USERNAME_ERROR);


        $password = $this->params['password'];
            if(!match_password($password))
                $this->error_json(\Common\Controller\ERROR_PASSWORD_ERROR);

    }


}