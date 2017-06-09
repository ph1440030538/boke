<?php
namespace app\admin\controller;
use think\Controller;
use app\common\model\MSetting;
use think\Request;
use think\Db;

class Setting extends Admin
{
	protected $Model;

	public function __construct(){
		$this->Model = new MSetting(); 
		parent::__construct();
	}

	/**
	 * 列表页面
	 */
	public function index(){
		$list = $this->Model->getList(Request::instance()->post());
		return view('index',[
			'list' => $list,
		]);
	}

	public function add(){
		if(Request::instance()->isPost()){
			$post = Request::instance()->post();
			if(MSetting::where(['setting_key'=>$post['setting_key']])->count()>0){
				$this->error("已经存在该标识");
			}

			$result = $this->Model->allowField(true)->validate(true)->save($post);

			if(false === $result){
			    $this->error($this->Model->getError());
			}else{
				$this->success("成功！");
			}
		}else{

			return view('add',[]);
		}
	}

	public function edit(){
		if(Request::instance()->isPost()){
			$post = Request::instance()->post();
			if(MSetting::where(['id'=>['neq',$post['id']],'setting_key'=>$post['setting_key']])->count()>0){
				$this->error("已经存在该标识");
			}

			$result = $this->Model->allowField(true)->validate(true)->save($post,['id'=>$post['id']]);

			if(false === $result){
			    // 验证失败 输出错误信息
			    $this->error($this->Model->getError());
			}else{
				$this->success("成功！");
			}
		}else{
			$id = Request::instance()->get('id/d',0);
			$model = $this->findOne($id);
			// var_dump( $model );die();
			return view('edit',[ 'model' => $model]);
		}
	}

	/**
	 * 删除
	 */
	public function deleteAll(){
		if($this->Model->deleteAll($_POST['id']) > 0){
			return json(['status'=>200,'msg'=>'成功']);
		}else{
			return json(['status'=>400,'msg'=>'失败']);
		}
	}

	public function findOne($id){
		return MSetting::find()->where("id",$id)->find();
	}
}
