<?php
    namespace app\index\controller;
    use think\Controller;
    use think\Loader;
    use think\Db;
    use think\Session;
    
    class Adminmain extends Controller {
        public function index() {
            $username=Session::get('username');
            if (!$username) $this->error('还未登录','adminlogin/index');
            $this->assign('username',$username);
            return $this->fetch();
        }
        //修改密码
        public function secret(){
            $username=Session::get('username');
            if (!$username) $this->error('还未登录','adminlogin/index');
            $this->assign('username',$username);
            return $this->fetch();
        }
        public function secret_update(){
            $username=Session::get('username');
            if (!$username) $this->error('还未登录','adminlogin/index');
            $this->assign('username',$username);
            $username=input('post.username');
            $password=input('post.password');
            $res = Db::name('user')->where('username',$username)->update(['password'=>$password]);
            $this->success('修改密码成功', 'index');
        }
        //处理登出
        public function logout(){
            Session::set('username', null);
            $this->success('退出成功', 'adminlogin/index');
        }
        //信息查询界面
        public function query(){
            $username=Session::get('username');
            if (!$username) $this->error('还未登录','adminlogin/index');
            $this->assign('username',$username);
            $access = db('user')->field('authority')->where('username', $username)->find();
            if($access['authority']>3){
                ?> 
                <script type="text/javascript">
                alert("您无权限进行该操作！");
                window.location.href="index";
                </script>
                <?php 
            }
            $lists = db('classroom')->select();
            $this->assign('lists',$lists);
            return $this->fetch();
        }
        //学生管理界面
        public function student(){
            $username=Session::get('username');
            if (!$username) $this->error('还未登录','adminlogin/index');
            $this->assign('username',$username);
            $access = db('user')->field('authority')->where('username', $username)->find();
            if($access['authority']>2){
                ?> 
                <script type="text/javascript">
                alert("您无权限进行该操作！");
                window.location.href="index";
                </script>
                <?php 
            }
            $lists = db('student')->select();
            $this->assign('lists',$lists);
            return $this->fetch();
        }
        public function student_add(){
            $username=Session::get('username');
            if (!$username) $this->error('还未登录','adminlogin/index');
            $this->assign('username',$username);
            $access = db('user')->field('authority')->where('username', $username)->find();
            if($access['authority']>2){
                ?> 
                <script type="text/javascript">
                alert("您无权限进行该操作！");
                window.location.href="index";
                </script>
                <?php 
            }
            return $this->fetch();
        }
        public function student_insert(){
            $username=Session::get('username');
            if (!$username) $this->error('还未登录','adminlogin/index');
            $this->assign('username',$username);
            $access = db('user')->field('authority')->where('username', $username)->find();
            if($access['authority']>2){
                ?> 
                <script type="text/javascript">
                alert("您无权限进行该操作！");
                window.location.href="index";
                </script>
                <?php 
            }
            $StudentID=input('post.StudentID');
            $password=input('post.password');
            $info = db('student')->where('StudentID', $StudentID)->find();
            if($info){
                ?> 
                <script type="text/javascript">
                alert("该学生已存在！");
                window.location.href="student_add";
                </script>
                <?php 
            }
            else{
                $data = [
                    'StudentID' => $StudentID,
                    'password'  => $password,
                ];
                $res = Db::table('student')->insert($data);
                if($res){
                    ?> 
                    <script type="text/javascript">
                    alert("新增成功！");
                    window.location.href="student";
                    </script>
                    <?php
                }
                else{
                    ?> 
                    <script type="text/javascript">
                    alert("新增失败！");
                    window.location.href="student_add";
                    </script>
                    <?php
                }
            }
        }
        public function student_change(){
            $username=Session::get('username');
            if (!$username) $this->error('还未登录','adminlogin/index');
            $this->assign('username',$username);
            $access = db('user')->field('authority')->where('username', $username)->find();
            if($access['authority']>2){
                ?> 
                <script type="text/javascript">
                alert("您无权限进行该操作！");
                window.location.href="index";
                </script>
                <?php 
            }
            $student = db('student')->where('StudentID',$_GET["id"])->find();
            $this->assign('StudentID',$student['StudentID']);
            $this->assign('password',$student['password']);
            $this->assign('room',$student['room']);
            return $this->fetch();
        }
        public function student_update(){
            $username=Session::get('username');
            if (!$username) $this->error('还未登录','adminlogin/index');
            $this->assign('username',$username);
            $access = db('user')->field('authority')->where('username', $username)->find();
            if($access['authority']>2){
                ?> 
                <script type="text/javascript">
                alert("您无权限进行该操作！");
                window.location.href="index";
                </script>
                <?php 
            }
            $StudentID=input('post.StudentID');
            $password=input('post.password');
            $room=input('post.room');
            $classroom = db('classroom')->where('room', $room)->find();
            if(!$classroom){
                ?> 
                <script type="text/javascript">
                alert("该房间不存在！");
                window.location.href="student";
                </script>
                <?php
            }
            else{
                if($classroom['seats']==$classroom['allocated']){
                    ?> 
                    <script type="text/javascript">
                    alert("该房间床位不足！");
                    window.location.href="student";
                    </script>
                    <?php 
                }
                else{
                    Db::table('classroom')->where('room', $room)->setInc('allocated');
                    $res=Db::table('student')->where('StudentID', $StudentID)->update(['password' => $password,'room' => $room]);
                    if(!$res){
                        ?> 
                        <script type="text/javascript">
                        alert("更新失败！");
                        window.location.href="student";
                        </script>
                        <?php 
                    }
                    else{
                        ?> 
                        <script type="text/javascript">
                        alert("更新成功！");
                        window.location.href="student";
                        </script>
                        <?php 
                    }
                }
            }
        }
        public function student_delete(){
            $username=Session::get('username');
            if (!$username) $this->error('还未登录','adminlogin/index');
            $this->assign('username',$username);
            $access = db('user')->field('authority')->where('username', $username)->find();
            if($access['authority']>2){
                ?> 
                <script type="text/javascript">
                alert("您无权限进行该操作！");
                window.location.href="index";
                </script>
                <?php 
            }
            $student = db('student')->field('room')->where('StudentID',$_GET["id"])->find();    //查到床位
            Db::table('classroom')->where('room', $student['room'])->setDec('allocated');            //空出床位
            $res = Db::table('student')->where('StudentID',$_GET["id"])->delete();              //删除学生
            if($res){
                ?> 
                <script type="text/javascript">
                alert("退宿成功！");
                window.location.href="student";
                </script>
                <?php 
            }
            else{
                ?> 
                <script type="text/javascript">
                alert("退宿失败！");
                window.location.href="student";
                </script>
                <?php
            }
        }
        //宿舍管理界面
        public function classroom(){
            $username=Session::get('username');
            if (!$username) $this->error('还未登录','adminlogin/index');
            $this->assign('username',$username);
            $access = db('user')->field('authority')->where('username', $username)->find();
            if($access['authority']>2){
                ?> 
                <script type="text/javascript">
                alert("您无权限进行该操作！");
                window.location.href="index";
                </script>
                <?php
            }
            $lists = db('classroom')->select();
            $this->assign('lists',$lists);
            return $this->fetch();
        }
        public function classroom_add(){
            $username=Session::get('username');
            if (!$username) $this->error('还未登录','adminlogin/index');
            $this->assign('username',$username);
            $access = db('user')->field('authority')->where('username', $username)->find();
            if($access['authority']>2){
                ?> 
                <script type="text/javascript">
                alert("您无权限进行该操作！");
                window.location.href="index";
                </script>
                <?php 
            }
            return $this->fetch();
        }
        public function classroom_insert(){
            $username=Session::get('username');
            if (!$username) $this->error('还未登录','adminlogin/index');
            $this->assign('username',$username);
            $access = db('user')->field('authority')->where('username', $username)->find();
            if($access['authority']>2){
                ?> 
                <script type="text/javascript">
                alert("您无权限进行该操作！");
                window.location.href="index";
                </script>
                <?php 
            }
            $building=input('post.building');
            $floor=input('post.floor');
            $room=input('post.room');
            $seats=input('post.seats');
            $info = db('classroom')->where('room', $room)->find();
            if($info){
                ?> 
                <script type="text/javascript">
                alert("该宿舍已存在！");
                window.location.href="classroom_add";
                </script>
                <?php 
            }
            else{
                $data = [
                    'building' => $building,
                    'floor'    => $floor,
                    'room'     => $room,
                    'seats'     => $seats,
                    'allocated'=> 0,
                ];
                $res = Db::table('classroom')->insert($data);
                if($res){
                    ?> 
                    <script type="text/javascript">
                    alert("新增成功！");
                    window.location.href="classroom";
                    </script>
                    <?php
                }
                else{
                    ?> 
                    <script type="text/javascript">
                    alert("新增失败！");
                    window.location.href="classroom_add";
                    </script>
                    <?php
                }
            }
        }
        public function classroom_change(){
            $username=Session::get('username');
            if (!$username) $this->error('还未登录','adminlogin/index');
            $this->assign('username',$username);
            $access = db('user')->field('authority')->where('username', $username)->find();
            if($access['authority']>2){
                ?> 
                <script type="text/javascript">
                alert("您无权限进行该操作！");
                window.location.href="index";
                </script>
                <?php 
            }
            $classroom = db('classroom')->where('room',$_GET["id"])->find();
            $this->assign('building',$classroom['building']);
            $this->assign('floor',$classroom['floor']);
            $this->assign('room',$classroom['room']);
            $this->assign('seats',$classroom['seats']);
            $this->assign('allocated',$classroom['allocated']);
            return $this->fetch();
        }
        public function classroom_update(){
            $username=Session::get('username');
            if (!$username) $this->error('还未登录','adminlogin/index');
            $this->assign('username',$username);
            $access = db('user')->field('authority')->where('username', $username)->find();
            if($access['authority']>2){
                ?> 
                <script type="text/javascript">
                alert("您无权限进行该操作！");
                window.location.href="index";
                </script>
                <?php 
            }
            $building=input('post.building');
            $floor=input('post.floor');
            $room=input('post.room');
            $seats=input('post.seats');
            $allocated=input('post.allocated');
            $classroom = db('classroom')->where('room', $room)->find();
            if($seats<$classroom['allocated']){
                ?> 
                <script type="text/javascript">
                alert("减少床位前需先为学生调换宿舍！");
                window.location.href="classroom";
                </script>
                <?php
            }
            else{
                $res=Db::table('classroom')->where('room', $room)->update(['seats' => $seats]);
                if(!$res){
                    ?> 
                    <script type="text/javascript">
                    alert("修改失败！");
                    window.location.href="classroom";
                    </script>
                    <?php 
                }
                else{
                    ?> 
                    <script type="text/javascript">
                    alert("修改成功！");
                    window.location.href="classroom";
                    </script>
                    <?php 
                }
            }
        }
        public function classroom_delete(){
            $username=Session::get('username');
            if (!$username) $this->error('还未登录','adminlogin/index');
            $this->assign('username',$username);
            $access = db('user')->field('authority')->where('username', $username)->find();
            if($access['authority']>2){
                ?> 
                <script type="text/javascript">
                alert("您无权限进行该操作！");
                window.location.href="index";
                </script>
                <?php 
            }
            $classroom = db('classroom')->where('room',$_GET["id"])->find();
            if($classroom['allocated']>0){
                ?> 
                <script type="text/javascript">
                alert("收回宿舍前需先为学生调换宿舍或退宿！");
                window.location.href="classroom";
                </script>
                <?php 
            }
            else{
                $res = Db::table('classroom')->where('room',$_GET["id"])->delete();
                if($res){
                    ?> 
                    <script type="text/javascript">
                    alert("收回成功！");
                    window.location.href="classroom";
                    </script>
                    <?php 
                }
                else{
                    ?> 
                     <script type="text/javascript">
                    alert("收回失败！");
                    window.location.href="classroom";
                    </script>
                    <?php
                }
            }
        }
        //用户管理界面
        public function user(){
            $username=Session::get('username');
            if (!$username) $this->error('还未登录','adminlogin/index');
            $this->assign('username',$username);
            $access = db('user')->field('authority')->where('username', $username)->find();
            if($access['authority']>1){
                ?> 
                <script type="text/javascript">
                alert("您无权限进行该操作！");
                window.location.href="index";
                </script>
                <?php
            }
            $lists = db('user')->select();
            $this->assign('lists',$lists);
            return $this->fetch();
        }
        public function user_add(){
            $username=Session::get('username');
            if (!$username) $this->error('还未登录','adminlogin/index');
            $this->assign('username',$username);
            $access = db('user')->field('authority')->where('username', $username)->find();
            if($access['authority']>1){
                ?> 
                <script type="text/javascript">
                alert("您无权限进行该操作！");
                window.location.href="index";
                </script>
                <?php 
            }
            return $this->fetch();
        }
        public function user_insert(){
            $username=Session::get('username');
            if (!$username) $this->error('还未登录','adminlogin/index');
            $this->assign('username',$username);
            $access = db('user')->field('authority')->where('username', $username)->find();
            if($access['authority']>1){
                ?> 
                <script type="text/javascript">
                alert("您无权限进行该操作！");
                window.location.href="index";
                </script>
                <?php 
            }
            $username=input('post.username');
            $password=input('post.password');
            $authority=input('post.authority');
            $info = db('user')->where('username', $username)->find();
            if($info){
                ?> 
                <script type="text/javascript">
                alert("该学生已存在！");
                window.location.href="user_add";
                </script>
                <?php 
            }
            else{
                $data = [
                    'username' => $username,
                    'password' => $password,
                    'authority'=> $authority,
                ];
                $res = Db::table('user')->insert($data);
                if($res){
                    ?> 
                    <script type="text/javascript">
                    alert("新增成功！");
                    window.location.href="user";
                    </script>
                    <?php
                }
                else{
                    ?> 
                    <script type="text/javascript">
                    alert("新增失败！");
                    window.location.href="user_add";
                    </script>
                    <?php
                }
            }
        }
        public function user_change(){
            $username=Session::get('username');
            if (!$username) $this->error('还未登录','adminlogin/index');
            $this->assign('username',$username);
            $access = db('user')->field('authority')->where('username', $username)->find();
            if($access['authority']>1){
                ?> 
                <script type="text/javascript">
                alert("您无权限进行该操作！");
                window.location.href="index";
                </script>
                <?php 
            }
            $user = db('user')->where('username',$_GET["id"])->find();
            $this->assign('username',$user['username']);
            $this->assign('password',$user['password']);
            $this->assign('authority',$user['authority']);
            return $this->fetch();
        }
        public function user_update(){
            $username=Session::get('username');
            if (!$username) $this->error('还未登录','adminlogin/index');
            $this->assign('username',$username);
            $access = db('user')->field('authority')->where('username', $username)->find();
            if($access['authority']>1){
                ?> 
                <script type="text/javascript">
                alert("您无权限进行该操作！");
                window.location.href="index";
                </script>
                <?php 
            }
            $username=input('post.username');
            $password=input('post.password');
            $authority=input('post.authority');
            $res=Db::table('user')->where('username', $username)->update(['password' => $password, 'authority' => $authority]);
            if(!$res){
                ?> 
                <script type="text/javascript">
                alert("修改失败！");
                window.location.href="user";
                </script>
                <?php 
            }
            else{
                ?> 
                <script type="text/javascript">
                alert("修改成功！");
                window.location.href="user";
                </script>
                <?php 
            }
        }
        public function user_delete(){
            $username=Session::get('username');
            if (!$username) $this->error('还未登录','adminlogin/index');
            $this->assign('username',$username);
            $access = db('user')->field('authority')->where('username', $username)->find();
            if($access['authority']>1){
                ?> 
                <script type="text/javascript">
                alert("您无权限进行该操作！");
                window.location.href="index";
                </script>
                <?php 
            }
            $res = Db::table('user')->where('username',$_GET["id"])->delete();
            if($res){
                ?> 
                <script type="text/javascript">
                alert("删除成功！");
                window.location.href="user";
                </script>
                <?php 
            }
            else{
                ?> 
                <script type="text/javascript">
                alert("删除失败！");
                window.location.href="user";
                </script>
                <?php
            }
        }
    }
