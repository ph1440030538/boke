<?php
namespace app\admin\controller;
use think\Controller;
use think\Session;

class Base extends Controller
{
	public function __construct(){
		$user = Session::get('user');
		if(!isset($user)){
			$this->redirect('admin/common/login');
		}

		parent::__construct();
	}

}
