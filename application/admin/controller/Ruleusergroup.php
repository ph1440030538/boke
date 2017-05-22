<?php
namespace app\admin\controller;
use think\Request;
use think\Db;
use app\common\model\MRuleusergroup;

class Ruleusergroup extends Admin
{
	private $Model;

	public function __construct(){
		$this->Model = new MRuleusergroup; 
		parent::__construct();
	}

	/*首页*/
	public function index(){

		$list = $this->Model->getList(Request::instance()->get());

		return view('index',[
			'list' => $list,
			'search' => Request::instance()->param()
		]);
	}

	/*添加页面*/
	public function add(){
		if(Request::instance()->isPost()){
			$post = Request::instance()->post();
			$post['rule_ids'] = isset($post['rule_ids'])&&is_array($post['rule_ids']) ? json_encode(array_values($post['rule_ids'])) : '[]';
			$post['user_group_ids'] = isset($post['user_group_ids'])&&is_array($post['user_group_ids']) ? json_encode(array_values($post['user_group_ids'])) : '[]';
			$post['group_ids'] = isset($post['group_ids'])&&is_array($post['group_ids']) ? json_encode(array_values($post['group_ids'])) : '[]';
			$result = $this->Model->allowField(true)->validate(true)->save($post);

			if(false === $result){
			    $this->error($this->Model->getError());
			}else{
				$this->success("成功！");
			}
			
		}else{
			//获取权限列表
			$rule = Db::table("boke_rule")->where(['status'=>['neq',111]])->select();
			//获取权限组
			$rule_group = Db::table("boke_rule_group")->where(['status'=>['neq',111]])->select();
			//获取角色
			$rule_user_group = Db::table("boke_rule_user_group")->where(['status'=>['neq',111],'uid'=>0])->select();

			// dump( $rule_group );;die();
			return view('add',[
				'rule'=>json_encode( $rule ),
				'rule_group'=> $rule_group,
				'rule_user_group'=> $rule_user_group,
			]);
		}
	}

	/*编辑页面*/
	public function edit(){
		if(Request::instance()->isPost()){
			$post = Request::instance()->post();
			$post['rule_ids'] = isset($post['rule_ids'])&&is_array($post['rule_ids']) ? json_encode(array_values($post['rule_ids'])) : '[]';
			$post['user_group_ids'] = isset($post['user_group_ids'])&&is_array($post['user_group_ids']) ? json_encode(array_values($post['user_group_ids'])) : '[]';
			$post['group_ids'] = isset($post['group_ids'])&&is_array($post['group_ids']) ? json_encode(array_values($post['group_ids'])) : '[]';
			
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

			//获取权限列表
			$rule_ids = json_decode($model['rule_ids']);
			// dump( $rule_ids );die();
			$rule = Db::table("boke_rule")->where(['status'=>['neq',111]])->select();
			foreach ($rule as $key => $vo) {
				$rule[$key]['selected'] = in_array($vo['id'], $rule_ids);
			}

			//获取权限组
			$group_ids = json_decode($model['group_ids']);
			$rule_group = Db::table("boke_rule_group")->where(['status'=>['neq',111]])->select();
			foreach ($rule_group as $key => $vo) {
				$rule_group[$key]['selected'] = in_array($vo['id'], $group_ids);
			}

			//获取角色
			$user_group_ids = json_decode($model['user_group_ids']);
			$rule_user_group = Db::table("boke_rule_user_group")->where([
				'status'=>['neq',111],
				'uid'=>0,
				'id'=>['neq',$id]
			])->select();
			foreach ($rule_user_group as $key => $vo) {
				$rule_user_group[$key]['selected'] = in_array($vo['id'], $user_group_ids);
			}

			return view('edit',[ 
				'model' => $model,
				'rule'=>json_encode( $rule ),
				'rule_group'=> $rule_group,
				'rule_user_group'=> $rule_user_group
			]);
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
		return MRuleusergroup::find()->where("id",$id)->find();
	}
}
