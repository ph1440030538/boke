<?php
namespace app\admin\controller;
use think\Request;
use think\Db;
use app\common\model\MCategory;

class Category extends Admin
{
	private $pageSize = 15;

	public $Model;
	public function __construct(){
		$this->Model = new MCategory(); 
		parent::__construct();
	}

	//首页
	public function index(){
		$list = $this->Model->getList();


		return view('index',[
			'list' => $list,
		]);
	}

	//添加
	public function add(){
		if(Request::instance()->isPost()){
			$post = input('post.');
			$result = $this->Model->validate(true)->save($post);

			if(false === $result){
			    // 验证失败 输出错误信息
			    $this->error($this->Model->getError());
			}else{
				$this->success("成功！");
			}
			
		}else{
			$listTpl = $this->Model->getListTpl();
			$pageTpl = $this->Model->getPageTpl();

			$category = $this->Model->getList();


			$id = input('get.id');
			return view('add',[
				'pageTpl' => $pageTpl,
				'listTpl' => $listTpl,
				'categoryList' => $category,
				'id' => $id
			]);
		}
		
	}

	//编辑
	public function edit(){
		if(Request::instance()->isPost()){
			$post = input('post.');
			//判断添加的菜单是不是自己或自己的子集
			if(in_array($post['parent_id'], $this->Model->get_child($post['id']))){
				$this->error('不可以加到该分类下！');
			}

			
			$result = $this->Model->validate(true)->save($post,['id'=>$post['id']]);

			if(false === $result){
			    // 验证失败 输出错误信息
			    $this->error($this->Model->getError());
			}else{
				$this->success("成功！");
			}
		}else{
			$id = Request::instance()->get('id/d',0);
			$model = MCategory::find()->where("id",$id)->find();
			if(empty($model)){
				exit('不存在该记录！');
			}
			$model = $model->toArray();
			$listTpl = $this->Model->getListTpl();
			$pageTpl = $this->Model->getPageTpl();
			$category = $this->Model->getList();

			// dump($model);die();
			return view('edit',[
				'model' => $model,
				'pageTpl' => $pageTpl,
				'listTpl' => $listTpl,
				'categoryList' => $category,
			]);
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



}
