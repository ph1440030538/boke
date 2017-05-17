<?php
namespace app\admin\controller;
use think\Controller;
use app\common\model\MSetting;
use think\Request;
use think\Db;

class Setting extends Base
{
	protected $Model;

	public function __construct(){
		$this->Model = new MSetting(); 
		parent::__construct();
	}

	/**
	 * 列表页面
	 */
	public function web(){
		return view('web',[]);
	}

	public function add(){
		if(Request::instance()->isPost()){
			$post = Request::instance()->post();
			$result = $this->Model->validate(true)->save($post);

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
			$result = $this->Model->validate(true)->save($post,['id'=>$post['id']]);

			if(false === $result){
			    // 验证失败 输出错误信息
			    $this->error($this->Model->getError());
			}else{
				$this->success("成功！");
			}
		}else{
			$id = Request::instance()->get('id/d');
			$model = $this->findOne($id);
			$option = $this->MCategory->getOption($model['category_id']);
			
			return view('edit',[ 'model' => $model,'option'=>$option ]);
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
		return MBokearticle::find()->where("id",$id)->find();
	}

}
