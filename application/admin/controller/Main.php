<?php
namespace app\admin\controller;
use think\Cache;
use think\Session;
use think\Request;
use app\common\model\MRulemenu;
use app\common\model\MUser;

class Main extends Admin
{
    public function index()
    {
    	$this->view->engine->layout(false);
        
        $user = Session::get('user');
        $userMenu = (new MRulemenu())->getUserMenu($user['id']);
        $userMenu = $this->getAdminMenu($userMenu);

        return view('index',[
            'userMenu'=> $userMenu
        ]);
    }

    //欢迎页
    public function content(){
        $system_version = $_SERVER["SERVER_SOFTWARE"];      //获取服务器标识的字串
        $php_version = PHP_VERSION;                   //获取PHP服务器版本
        //从PHP配置文件中获得最大上传限制
        $max_upload = ini_get("file_uploads") ? ini_get("upload_max_filesize") : "Disabled";
        //从PHP配置文件中获得脚本的最大执行时间
        $max_ex_time= ini_get("max_execution_time")."秒";
        //以下两条获取服务器时间，中国大陆采用的是东八区的时间,设置时区写成Etc/GMT-8
        date_default_timezone_set("Etc/GMT-8");
        $systemtime = date("Y-m-d H:i:s",time());

        // var_dump( $system_version );die();
        return view('content',[
            'system_version' => $system_version,
            'php_version' => $php_version,
            'max_upload' => $max_upload,
            'max_ex_time' => $max_ex_time,
            'systemtime' => $systemtime,
        ]);
    }

    //个人设置
    public function user(){
        $MUser = new MUser();
        $user = Session::get('user');

        if(Request::instance()->isPost()){
            $post = Request::instance()->post();
            if(empty($post['password'])){
                $this->error("密码不能为空！");
            }

            if($post['password'] !=$post['repassword']){
                $this->error("两遍密码不一致！");
            }

            $result = $MUser->changePassword($user['id'], $post['password'], $post['repassword']);
            if($result){
                $this->success("修改密码成功！");
            }else{
                $this->error("修改密码失败！");
            }
        }else{
            $user = $MUser->getUserById($user['id']);
            return view('user',['user'=>$user]);
        }
    }

    //网站设置
    public function web(){

    }


    private function getAdminMenu($userMenu){
        if(empty($userMenu)){
            return;
        }
        $html = "";
        foreach ($userMenu as $key => $menu) {
            if(empty($menu['children'])){
                $html .= "<el-menu-item index='{$menu['id']}' v-on:click=\"handleSkipUrl('{$menu['href']}','{$menu['title']}',{$menu['id']})\"><i class=\"el-icon-message\"></i>{$menu['title']}</el-menu-item>";
            }else{
                $html .= "<el-submenu index='{$menu['id']}'>";
                $html .= "<template slot=\"title\"><i class=\"el-icon-message\"></i>{$menu['title']}</template>";
                $html .= $this->getAdminMenu($menu['children']);
                $html .= "</el-submenu>";
            }
        }
        return $html;
    }

}
