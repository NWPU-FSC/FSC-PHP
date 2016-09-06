<?php
namespace Common\Model;

use Think\Model;
use Think\Model\RelationModel;

class UserModel extends RelationModel{


    protected $_link = array(
        'Department' => array(
            'mapping_type' => self::MANY_TO_MANY,
            'relation_table' => 'teacher_department',
            'foreign_key' => 'teacher_id',
            'relation_foreign_key' => 'department_id'
        ),
        'Access' => array(
            'mapping_type' => self::BELONGS_TO,
            'foreign_key' => 'access',
            'as_fields' => 'name:access_name,remark:access_remark',
        ),
        'Class' => array(
            'mapping_name' => 'class',
            'class_name' => 'Class',
            'mapping_type' => self::MANY_TO_MANY,
            'relation_table' => 'student_class',
            'foreign_key' => 'student_id',
            'relation_foreign_key' => 'class_id',
            //按照入班的时间顺序排序 数据库里加了东西
            //'mapping_order' => 'time DESC',
            'relation_deep' => 'Homework'
        ),
        'Award' =>array(
            'mapping_name'   => 'award',
            'class_name'     => 'Award',
            'mapping_type'   => self::MANY_TO_MANY,
            'relation_table' => 'student_award',
            'foreign_key'    => 'student_id',
            'relation_foreign_key' => 'award_id',
            'relation_deep'  => 'AwardType'

        ),
        'ParentStudent' => array(
            'mapping_type'   => self::MANY_TO_MANY,
            'mapping_name' => 'parent_student',
            'class_name' => 'User',
            'relation_table' => 'parent_student',
            'foreign_key' => 'parent_id',
            'relation_foreign_key' => 'student_id',
            'relation_deep' => 'RelationType'
        ),

    );


    public function find_user_by_name($username){
        return $this->where("username = '$username'")->find();
    }

    public function teacher_message($id){
        return $this->field('name,email,phone,school,education,signature')->find($id);
    }

    //请进行参数检查
    public function add_user($params){
        $params = filter_params($params,array('name','username','password','status','access','gender','email','phone','remark'));

        $params['secret']=bin2hex(random_bytes(16));    //创建一个随机值作为盐
        $params['password']=$this->hash_password($params['password'],$params['secret']); //加密密码
        return $this->add($params);
    }

    public function delete_user($id){

        return $this->query("delete from user where id = $id");
    }

    //判断用户名是否存在后再调用这个函数
    public function user_login($username,$password)
    {
        $data = $this->where("username = '$username'")->find();
        return $data && $data['password'] == $this->hash_password($password, $data['secret']);
    }

    public function user_mod_password($username,$password)
    {
        $data = $this->where("username = '$username'")->find();
        $password = $this->hash_password($password,$data['secret']);
        return $this->where("id={$data['id']}")->setField('password',$password);
    }

    public function add_parent($params){
        $params['access'] = C('DEFAULT_PARENT_LEVEL');
        $params['status'] = 1;
        $this->startTrans();
        if($this->add_user($params))
            if($parent_id = $this->add()){
                $parent_student = M('ParentStudent');
                if($parent_student->add(array(
                    'parent_id' => $parent_id,
                    'student_id' => $params['student_id'],
                    'relation_id' => $params['relation_id']
                ))) {
                    if ($this->commit())
                        return true;
                }
            }
        $this->rollback();
        return false;
    }

    public function add_mul_student($params){
        $count = 0;
        $name = array();
        foreach ($params as $v){
            $v['access']=10;
            if($this->add_user($v)){
                $count++;
            }else{
                $name[] = $v['name'];
            }
        }
        return array(
            'count'=>$count,
            'name'=>$name,
        );
    }

    //获取学生班级的信息
    public function get_class($student_id){
        //relation里的参数的名字一定要跟mapping_name一样否则会找不到
        return $this->relation('class')->find($student_id)['class'];
    }

    //获取学生当前的班级
    public function get_current_class($student_id){
        return $this->get_class($student_id)[0];
    }

    private function hash_password($password,$secret){
        return md5(C('SECURE_KEY').$password.$secret);
    }

    //获取学生当前奖励
    public function award_list($student_id){
        return $this->relation('award')->where("id = $student_id")->find()['award'];
    }

    public function get_user($user_id){
        return $this->cache(true)->find($user_id);
    }


    public function get_student_contacts($student_id){
        $leader = D('StudentLeaderView');
        $array1 = $leader->field('id,name')->where("student_id = $student_id")->select();
        $teacher = D('StudentTeacherView');
        $array2 = $teacher->field('id,name')->where("student_id = $student_id")->select();

        return array_unique(array_merge($array1,$array2));
    }

    //获取老师联系人
    public function get_teacher_contacts($teacher_id){

        $Leader = D('LeaderParentView');
        $array1 = $Leader->field('id,name')->where("teacher_id = $teacher_id")->select();
        $Teacher = D('TeacherParentView');
        $array2 = $Teacher->field('id,name')->where("teacher_id = $teacher_id")->select();

        $data = array_merge($array1,$array2);

        return $this->array_unique_fb($data);

    }

    public function search_user($pix){
        $M = new Model();
        return $M->query("SELECT `id`,`name` FROM user WHERE name LIKE '$pix%'");
    }

    //二维数组去掉重复值 并保留键值
    function array_unique_fb($array2D)
    {
        foreach ($array2D as $k=>$v)
        {
            $v = join(",",$v); //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
            $temp[$k] = $v;
        }
        $temp = array_unique($temp); //去掉重复的字符串,也就是重复的一维数组
        $data = array();
        foreach ($temp as $k => $v)
        {
            $array=explode(",",$v); //再将拆开的数组重新组装
            $temp2["id"] =$array[0];
            $temp2["name"] =$array[1];
            array_push($data,$temp2);//将数据依次插入数组
        }
        return $data;
    }



}

