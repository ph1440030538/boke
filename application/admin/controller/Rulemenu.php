<?php
namespace app\admin\controller;
use think\Request;
use app\common\model\MRulemenu;

class Rulemenu extends Admin
{
	private $Model;

	public function __construct(){
		$this->Model = new MRulemenu; 
		parent::__construct();
	}

	/*首页*/
	public function index(){

		$list = $this->Model->getList(Request::instance()->get());

		// var_dump( $list );die();
		return view('index',[
			'list' => $list,
		]);
	}

	/*添加页面*/
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
			//获取菜单
			$id = Request::instance()->get('id/d');
			$option = $this->Model->getOption($id);
			// dump( $id  );die();
			return view('add',['option'=>$option]);
		}
	}

	/*编辑页面*/
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

			//获取菜单
			$option = $this->Model->getOption($model['parentid']);
			return view('edit',[ 'model' => $model,'option'=>$option ]);
		}
	}

	/*删除*/
	public function deleteAll(){
		if($this->Model->deleteAll($_POST['id']) > 0){
			return json(['status'=>200,'msg'=>'成功']);
		}else{
			return json(['status'=>400,'msg'=>'失败']);
		}
	}


	public function findOne($id){
		return MRulemenu::find()->where("id",$id)->find();
	}
}
