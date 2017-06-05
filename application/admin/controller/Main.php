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
        echo 'content';
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
