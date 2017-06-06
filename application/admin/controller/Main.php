<?php
namespace app\admin\controller;
use think\Cache;
use think\Session;
use app\common\model\MRulemenu;

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

    public function content(){
        $sysos = $_SERVER["SERVER_SOFTWARE"];      //获取服务器标识的字串
        $sysversion = PHP_VERSION;                   //获取PHP服务器版本
        //从服务器中获取GD库的信息
        if(function_exists("gd_info")){                  
        $gd = gd_info();
            $gdinfo = $gd['GD Version'];
        }else {
            $gdinfo = "未知";
        }
        //从GD库中查看是否支持FreeType字体
        $freetype = $gd["FreeType Support"] ? "支持" : "不支持";
        //从PHP配置文件中获得是否可以远程文件获取
        $allowurl= ini_get("allow_url_fopen") ? "支持" : "不支持";
        //从PHP配置文件中获得最大上传限制
        $max_upload = ini_get("file_uploads") ? ini_get("upload_max_filesize") : "Disabled";
        //从PHP配置文件中获得脚本的最大执行时间
        $max_ex_time= ini_get("max_execution_time")."秒";
        //以下两条获取服务器时间，中国大陆采用的是东八区的时间,设置时区写成Etc/GMT-8
        date_default_timezone_set("Etc/GMT-8");
        $systemtime = date("Y-m-d H:i:s",time());
        return view('content',[
        ]);
    }


    public function getAdminMenu($userMenu){
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
