<?php
namespace app\admin\controller;
use think\Request;
use think\Db;
use app\common\model\MMenu;

class Menu extends Base
{
	private $pageSize = 15;

	public $Model;
	public function __construct(){
		$this->Model = new MMenu(); 
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
			$post = Request::instance()->post();
			$result = $this->Model->validate(true)->save($post);

			if(false === $result){
			    // 验证失败 输出错误信息
			    $this->error($this->Model->getError());
			}else{
				$this->success("成功！");
			}
			
		}else{
			$option = $this->Model->getOption();

			return view('add',[
				'option' => $option
			]);
		}
		
	}

	//编辑
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
			// var_dump( $model['parent_id'] );die();
			$option = $this->Model->getOption($model['parent_id']);
			
			return view('edit',[ 'model' => $model,'option'=>$option ]);
		}
	}

	//删除
	public function delete(){
		$id = (int)input('post.id',0);
		$result = Db::table('boke_category')->where('id', $id)->update(['update_time' => time(),'status'=>111]);
		if($result == 1){
			return json(['status'=>200,'msg'=>'成功']);
		}else{
			return json(['status'=>400,'msg'=>'失败']);
		}
	}

	public function findOne($id){
		return MMenu::find()->where("id",$id)->find();
	}
	
}
