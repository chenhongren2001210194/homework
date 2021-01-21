<?php
    namespace app\index\controller;
    use think\Controller;
    use think\Loader;
    use think\Db;
    use think\Session;
    
    class Login extends Controller {
        public function index() {
            return $this->fetch();
        }
       //处理登录 
        public function login() {
            $StudentID = input('post.StudentID');
            $password = input('post.password');
            $info = db('student')->where('StudentID', $StudentID)->find();
            if (!$info) {
                $this->error('用户名或密码错误1!');
            }
            if ($password != $info['password']) {
                $this->error('用户名或密码错误2!');
            } 
            else {
                Session::set('StudentID', $StudentID);
                $this->success('登入成功', 'main/index');
            }
        }
        //测试函数
        public function test(){
            echo 'test';
        }
    }
?>
