<?php
namespace app\admin\controller;
use think\Request;
use think\Db;
use app\common\model\MBokearticle;

class Boke extends Base
{
	private $pageSize = 20;
	private $Model;

	public function __construct(){
		$this->Model = new MBokearticle(); 
		parent::__construct();
	}

	//首页
	public function index(){
		$curPage = Request::instance()->get('page/d',1);
		$this->Model = $this->Model->where("status","neq",'111');

		// 获取总页数
		$total = $this->Model->count();
		$lists = $this->Model->page("{$curPage},{$this->pageSize}")->select();

		return view('index',[
			'lists' => $lists,
			'pages' => ceil($total/$this->pageSize),
			'curPage' => $curPage,
		]);
	}

	//添加
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
			return view('add');
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
			return view('edit',[ 'model' => $model ]);
		}
	}

	//删除
	public function delete(){
		$id = Request::instance()->get('id/d');
		$model = $this->findOne($id);
		$model->status = 111;
		if($model->save() == 1){
			return json(['status'=>200,'msg'=>'成功']);
		}else{
			return json(['status'=>400,'msg'=>'失败']);
		}
	}


	public function findOne($id){
		return MBokearticle::find()->where("id",$id)->find();
	}
}
