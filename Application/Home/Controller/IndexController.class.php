<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function _initialize() {
        $allow_actions = explode(',','Home.Index.login'); //配置哪些操作无需登录即可访问,比如登录，验证登录
        $curr_action = MODULE_NAME . '.' . CONTROLLER_NAME . '.' . ACTION_NAME;
        if(!in_array($curr_action,$allow_actions) && !session("?username")) { //未登录且是需要登录后访问的
            $this->redirect('index/login');
        }
        else{
            $this->assign("username",session("username"));
        }
    }
    public function index(){
        $this->redirect("user_manager");
    }
    public function loginByPassword(){
        $return_data = array();
        $mobile = I("mobile");
        $password = I("password");
        $model = M();
        $result = $model->query("SELECT * FROM login WHERE mobile='$mobile' AND password='$password'");
        if($result){
            $return_data["code"]=200;
            $return_data["msg"]="登录成功";
        }
    }
    public function user_manager(){
        $user = M("user");
        $this->assign("list",$user->select());
        $this->show();
    }
    public function add_user(){
        if(I('post.username')  && I('post.mobile')  && I('post.password') &&I('post.remark') ) {
            $data_['username'] = I('post.username');
            $data_['mobile'] = I('post.mobile');
            $data_['password'] = I('post.password');
            $data_['remark'] = I('post.remark');
            $data_['priority'] = 3;
            if (I('post.remark')>0)
                $data_['priority'] = I('post.priority');
            $data_['priority'] = I('post.priority');
    //        $data_['time'] = date('Y-m-d H:i:s',time());
            $Dao = M('user');
            if ($Dao->add($data_)) {
                $this->success('添加成功', 'user_manager');
            } else {
                $this->error('添加失败', 'user_manager');
            }
        }
        else{
            $this->error('添加失败', 'user_manager');
        }
    }
    public function delete_user(){
        //获得log的id
        $id = I("get.id","int");
        $devices = M('user');
        if($devices->where("id=$id")->select()[0]['priority'] == 0){
            $return_data['status'] = "fail";
            $this->ajaxReturn($return_data,'JSON');
            die();
        }
        $devices->where("id=$id")->delete();
        $return_data['status'] = "success";
        $return_data['id'] = $id;
        $this->ajaxReturn($return_data,'JSON');
    }
    public function login(){
        if (IS_GET){
            $this->show();
            exit();
        }
        $model = M("user");
        $username=$_POST["username"];
        $password=$_POST["password"];
        if ($username=="") {
            //echo "<script>alert('用户名为空')</script>";
            echo "<script language=\"JavaScript\">";
            echo "alert(\"用户名为空\");\r\n";
            echo "history.back();\r\n";
            echo "</script>";
            exit;
        }
        else if ($password=="")
        {
            //echo "<script>alert('密码为空')</script>";
            echo "<script language=\"JavaScript\">";
            echo "alert(\"密码为空\");\r\n";
            echo "history.back();\r\n";

            echo "</script>";
            exit;
        }

        else {
            $result = $model->query("SELECT * FROM user WHERE username='$username' AND password='$password'");
            if ($result) {
                //$su = "登陆成功";
                //重定向浏览器
                session('username',$result[0]["username"]);  //设置session
                $this->success('登录成功', 'user_manager');
                //确保重定向后，后续代码不会被执行
                exit;


            } else {
                echo "<script language=\"JavaScript\">";
                echo "alert(\"用户名或密码错误\");\r\n";
                echo "history.back();\r\n";
                echo "</script>";
                exit;
            }
        }
    }
    public function logout(){
        session("username",null);
        $this->success('退出成功！', 'index/login');
    }

}