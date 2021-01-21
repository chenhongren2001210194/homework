<?php
    namespace app\index\controller;
    use think\Controller;
    use think\Loader;
    use think\Db;
    use think\Session;
    
    class Adminlogin extends Controller {
        public function index() {
            return $this->fetch();
        }
       //处理登录 
        public function login() {
            $username = input('post.username');
            $password = input('post.password');
            $info = db('user')->field('username,password')->where('username', $username)->find();
            if (!$info) {
                $this->error('用户名或密码错误');
            }
            if ($password != $info['password']) {
                $this->error('用户名或密码错误');
            } 
            else {
                Session::set('username', $info['username']);
                $this->success('登入成功', 'adminmain/index');
            }
        }
        //测试函数
        public function test(){
            echo 'test';
        }
    }
?>
