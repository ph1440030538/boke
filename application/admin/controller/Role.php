<?php
namespace app\admin\controller;
use think\Request;
use think\Db;
use app\common\model\MRole;

class Role extends Base
{
	private $Model;

	public function __construct(){
		$this->Model = new MRole; 
		parent::__construct();
	}

	/*首页*/
	public function index(){

		$list = $this->Model->getList(Request::instance()->get());

		return view('index',[
			'list' => $list,
		]);
	}

	/*添加页面*/
	public function add(){
		if(Request::instance()->isPost()){
			$post = Request::instance()->post();
			$result = $this->Model->allowField(true)->validate(true)->save($post);

			if(false === $result){
			    $this->error($this->Model->getError());
			}else{
				$this->success("成功！");
			}
			
		}else{
			return view('add');
		}
	}

	/*编辑页面*/
	public function edit(){
		if(Request::instance()->isPost()){
			$post = Request::instance()->post();
			$result = $this->Model->allowField(true)->validate(true)->save($post,['id'=>$post['id']]);

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

	/*删除*/
	public function deleteAll(){
		if($this->Model->deleteAll($_POST['id']) > 0){
			return json(['status'=>200,'msg'=>'成功']);
		}else{
			return json(['status'=>400,'msg'=>'失败']);
		}
	}

	//增加用户
	public function adduser(){
		$where = [
			'status'=>['neq',111]
		];
		$users = Db::table("boke_user")->where($where)->order("id desc")->select();

		return view('adduser',[ 'users' => $users ]);
	}

	//增加用户
	public function auth(){
		
		
		
		return view('auth',[ 'users' => [] ]);
	}


	public function findOne($id){
		return MRole::find()->where("id",$id)->find();
	}
}
