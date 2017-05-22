<?php
namespace app\admin\controller;
use think\Controller;
use think\Session;
use app\common\model\MRule;

class Admin extends Controller
{
	public function __construct(){

		$user = Session::get('user');
		// if(!isset($user)){
		// 	$this->redirect('admin/common/login');
		// }
		
		if($this->isCheck() == false){
			exit( '---没有该权限---' );
		}

		parent::__construct();
	}

	/**
	 * 验证权限是否通过
	 */
	public function isCheck(){
		$MRule = new MRule();
		$request = \think\Request::instance();

		//获取拥有的权限
		$rule = $MRule->getRuleInfoByUid(41);
		$module = $request->module() ;
		$controller = strtolower($request->controller()) ;
		$action = $request->action();
		$request_params = $request->param();
		$request_url = "/{$module}/{$controller}/{$action}";
		$request_controller = "/{$module}/{$controller}/*";

		//验证params
		if(isset( $rule[$request_url])&&isset($rule[$request_url]['params'])){
			$params = $rule[$request_url]['params'];
			foreach ($params as $param) {
				$param_keys = array_keys($param);
				$flag = 0;
				foreach ($param_keys as $key) {
					if(isset($request_params[$key])&&$request_params[$key] == $param[$key]){
						$flag ++;
					}
				}
				if($flag == count($param_keys) - 1&&$param['isAccept'] == 0){
					return false;
				}else if($flag&&$param['isAccept'] == 1){
					return true;
				}
			}
		}
		//验证方法
		if(isset( $rule[$request_url] )&&isset( $rule[$request_url]['rule'])){

			return $rule[$request_url]['isAccept'] == 1 ? true: false;
		}
		//验证控制器
		if(isset( $rule[$request_controller] )){
			return $rule[$request_controller]['isAccept'] == 1 ? true: false;
		}
		return false;
	}

	/**
	 * role 权限
	 * @return [type] [description]
	 */
    public function logout(){
        Session::set('user',null);
        
    }
}
