<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;

class Upload extends Controller
{
	public function __construct(){
		parent::__construct();
	}

	public function image(){
	    $file = request()->file(key( request()->file() ));
	    // 移动到框架应用根目录/public/uploads/ 目录下
	    $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
	    $saveName = str_replace("\\","/",$info->getSaveName());
		// dump( $saveName );die();

		return json(['code'=>0,'msg'=>'success','data'=>['src'=>"/uploads/".$saveName]]);
	}

}
