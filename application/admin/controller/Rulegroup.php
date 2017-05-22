<?php
namespace app\admin\controller;
use think\Request;
use think\Db;
use app\common\model\MRulegroup;

class Rulegroup extends Admin
{
	private $Model;

	public function __construct(){
		$this->Model = new MRulegroup; 
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
			$post['rule_ids'] = isset($post['rule_ids'])&&is_array($post['rule_ids']) ? json_encode(array_values($post['rule_ids'])) : '[]';
			$result = $this->Model->allowField(true)->validate(true)->save($post);

			if(false === $result){
			    $this->error($this->Model->getError());
			}else{
				$this->success("成功！");
			}
			
		}else{
			//获取权限列表
			$rule = Db::table("boke_rule")->where(['status'=>['neq',111]])->select();

			return view('add',['rule'=>json_encode( $rule )]);
		}
	}

	/*编辑页面*/
	public function edit(){
		if(Request::instance()->isPost()){
			$post = Request::instance()->post();
			$post['rule_ids'] = isset($post['rule_ids'])&&is_array($post['rule_ids']) ? json_encode(array_values($post['rule_ids'])) : '[]';
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
			$select_rule = json_decode($model['rule_ids']);
			//获取权限列表
			$rule = Db::table("boke_rule")->where(['status'=>['neq',111]])->select();
			foreach ($rule as $key => $vo) {
				$rule[$key]['selected'] = in_array($vo['id'], $select_rule);
			}


			return view('edit',[ 'model' => $model ,'rule'=>json_encode( $rule )]);
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
		return MRulegroup::find()->where("id",$id)->find();
	}
}
