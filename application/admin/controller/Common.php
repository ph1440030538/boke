<?php
namespace app\admin\controller;
use think\Request;
use think\Session;
use think\Cache;
use think\Db;
use app\common\model\MUser;
use app\common\model\MRulemenu;
use think\Controller;

class Common extends Controller
{
	protected $Model;

	public function __construct(){
		$this->Model = new MUser(); 
		$user = Session::get('user');
		if(isset($user)){
			$this->redirect('admin/Main/index');
		}

		parent::__construct();
	}

    public function login()
    {
        $token = $this->request->token('__token__', 'sha1');
        $this->assign('token', $token);
        return view('login');
    }

    public function dologin(){
        Session::set('user','user');
    	$post = Request::instance()->post();
    	if(Session::get('__token__') != $post['__token__']){
    		return json(['status'=>400,'msg'=>'验证失败']);
    	}
    	if($this->Model->isCheckLogin($post) ===false){
    		return json(['status'=>400,'msg'=>'密码或账号错误']);
    	}
        //获取用户的权限
        $user = Session::get('user');
        $userRole = Db::table("boke_role_user")->where("uid",$user['id'])->find();
        $roleAccess = Db::table("boke_role_access")->where(['role_id'=>$userRole['role_id']])->select();
        $roleAccess = empty($roleAccess) ? '-1' : array_column($roleAccess, "menu_id");
        $ruleMenu = Db::table("boke_rule_menu")->where([
            'status'=>['neq',111],
            'id'=>['in', $roleAccess]
        ])->select();


        $MRulemenu = new MRulemenu();
        $menu = $MRulemenu->getMenu($ruleMenu);
        Session::set('ruleMenu',json_encode( $menu ));

        

    	return json(['status'=>200,'msg'=>'成功']);
    }

}
