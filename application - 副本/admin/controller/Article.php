<?php
namespace app\admin\controller;
use think\Controller;
use app\common\model\MBokearticle;
use app\common\model\MCategory;
use think\Request;
use think\Db;

class Article extends Admin
{
	protected $Model;
	protected $MCategory;

	public function __construct(){
		$this->Model = new MBokearticle(); 
		$this->MCategory = new MCategory();
		parent::__construct();
	}

	/**
	 * 列表页面
	 */
	public function index(){
		$list = $this->Model->getList(Request::instance()->get());

		$option = $this->MCategory->getOption();
		return view('index',[
			'list' => $list,
			'option' => $option
		]);
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
			$option = $this->MCategory->getOption();

			return view('add',['option'=>$option]);
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
