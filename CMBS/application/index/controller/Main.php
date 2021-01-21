<?php
    namespace app\index\controller;
    use think\Controller;
    use think\Loader;
    use think\Db;
    use think\Session;
    
    class Main extends Controller {
        public function index() {
            $StudentID=Session::get('StudentID');
            if (!$StudentID) $this->error('还未登录','login/index');
            $this->assign('StudentID',$StudentID);
            return $this->fetch();
        }
        //修改密码
        public function secret(){
            $StudentID=Session::get('StudentID');
            if (!$StudentID) $this->error('还未登录','login/index');
            $this->assign('StudentID',$StudentID);
            return $this->fetch();
        }
        public function secret_update(){
            $StudentID=Session::get('StudentID');
            if (!$StudentID) $this->error('还未登录','login/index');
            $this->assign('StudentID',$StudentID);
            $username=input('post.StudentID');
            $password=input('post.password');
            $res = Db::name('student')->where('StudentID',$StudentID)->update(['password'=>$password]);
            $this->success('修改密码成功', 'index');
        }
        //处理登出
        public function logout(){
            Session::set('StudentID', null);
            $this->success('退出成功', 'login/index');
        }
        //提交申请界面
        public function application(){
            $StudentID=Session::get('StudentID');
            if (!$StudentID) $this->error('还未登录','login/index');
            $this->assign('StudentID',$StudentID);
            return $this->fetch();
        }
        public function application_post(){
            $StudentID=Session::get('StudentID');
            if (!$StudentID) $this->error('还未登录','login/index');
            $this->assign('StudentID',$StudentID);
            return $this->fetch();
        }
        //查询结果界面
        public function query(){
            $StudentID=Session::get('StudentID');
            if (!$StudentID) $this->error('还未登录','login/index');
            $this->assign('StudentID',$StudentID);
            $info = db('student')->where('StudentID', $StudentID)->find();
            $this->assign('room',$info['room']);
            return $this->fetch();
        }
    }
