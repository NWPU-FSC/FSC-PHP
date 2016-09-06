<?php
/**
 * Created by PhpStorm.
 * User: Banixc
 * Date: 2016/8/24
 * Time: 14:12
 */

namespace Admin\Controller;

use Admin\Controller\Base\BaseAdminController;
use Boris\DumpInspector;
use Common\Model\ClassModel;
use Common\Model\UserModel;

class ImportController extends BaseAdminController{

    public function index(){
        if(!IS_POST){
            $this->_auth(C('REMOVE_NEWS_LEVEL'));
            $this->set_title("导入数据");
            $this->display();
        } else {
            $method = I('post.method');
            call_user_func(array($this, $method));
        }
    }

    //导入学生信息

    /**
       [
        {
        "name":"张三三",
        "username":"zh2angs1an2a",
        "password":"sss",
        "gender":"F"
        },

    {
    "name":"张三so",
    "username":"zh2an2gs1an2a",
    "password":"sssss",
    "gender":"M"
    }

        ]
     *
     *
     *
     */
    public function import_student_message(){
        $message = $this->get_import();
        $S = new UserModel();
        $data = $S->add_mul_student($message);
        $this->success("添加成功{$data['count']}个学生");
    }

    /**
     *
    [
    {
    "name":"三年级十班",
    "grade":10,
    "leader":1
    },
         {
    "name":"三年级2十班",
    "grade":10,
    "leader":89
    }
    ]
     *
     */


    //导入班级信息
    public function import_class_message(){
        $message = $this->get_import();
        $S = new ClassModel();
        $data = $S->add_mul_class($message);
        if($data)
            $data = count($message);
        $this->success("添加成功{$data}个班级");
    }


    /**
     *
    [
    {
    "student_id":1,
    "class_id":10
    },
          {
    "student_id":1,
    "class_id":11
    }
    ]



     */

    //导入学生所在的班级
    public function import_student_class(){
        $message = $this->get_import();
        $S = M('StudentClass');
        $data = $S->addAll($message);
        if($data)
            $data = count($message);
        $this->success("添加成功{$data}条数据");
    }

    /**
     *
    [
    {
    "name":"中期运动会考",
    "time":"2015-11-12"
    },
    {
    "name":"中期运动会考2",
    "time":"2015-11-12"
    }
    ]



     */

    //导入考试记录
    public function import_exam_message(){
        $message = $this->get_import();
        $S = M('Exam');
        $data = $S->addAll($message);
        if($data)
            $data = count($message);
        $this->success("添加成功{$data}场考试");
    }


    /***
     *
     *
     *
    [
    {
    "student_id":1,
    "exam_id":2,
     "course_id":1,
     "mark":100
    },
    {
    "student_id":1,
    "exam_id":2,
    "course_id":2,
    "mark":100
    }
    ]

     *
     *
     *
     */


    //导入学生成绩
    public function import_student_remark(){
        $message = $this->get_import();
        $S = M('Mark');
        $data = $S->addAll($message);
        if($data)
            $data = count($message);
        $this->success("添加成功{$data}条数据");
    }


    /**
     *
     *
    [
    {
    "student_id":1,
    "time":"2016-08-11 12:00:12"
    },
    {
    "student_id":1,
    "time":"2016-08-11 12:00:13"
    }
    ]
     *
     *
     */

    //导入学生考勤
    public function import_student_sign_up(){
        $message = $this->get_import();
        $S = M('Signin');
        $data = $S->addAll($message);
        $this->success("添加成功{$data}条数据");
    }

    private function get_import(){
        $request = html_entity_decode(I('post.data'));
//        $request = I('post.data');

//        dump($request);
//        $request = file_get_contents('php://input');

        if($data = json_decode($request, TRUE))
            return $data;
        $this->error("格式错误，请检查格式是否符合JSON的正确格式");
    }




}