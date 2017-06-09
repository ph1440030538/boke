<?php
namespace app\admin\controller;
use think\Request;
use think\Session;
use think\Cache;
use think\Db;
use app\common\model\MUser;
use app\common\model\MRulemenu;
use app\common\model\MRuleUserMenu;
use think\Controller;

class Common extends Controller
{
	protected $Model;

	public function __construct(){
		$this->Model = new MUser(); 
		$user = Session::get('user');
        $request = \think\Request::instance();
        $action = $request->action();
		if(isset($user)&&$action!='logout'){
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
    	$post = Request::instance()->post();
    	if(Session::get('__token__') != $post['__token__']){
    		return json(['status'=>400,'msg'=>'验证失败']);
    	}
    	if($this->Model->isCheckLogin($post) ===false){
    		return json(['status'=>400,'msg'=>'密码或账号错误']);
    	}
    	return json(['status'=>200,'msg'=>'成功']);
    }


    public function logout(){
        session::clear();
        $this->success('退出成功');
    }
}
