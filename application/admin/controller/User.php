<?php
namespace app\admin\controller;
use think\Request;
use think\Cache;
use think\Db;
use app\common\model\MUser;
use app\common\model\MRuleusergroup;
use app\common\model\MRuleUserMenu;
use extend\Tree;
use think\Loader;

class User extends Admin
{
	private $Model;

	public function __construct(){
		$this->Model = new MUser; 
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
			$post['password'] = $this->Model->encryption($post['password']);
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

	//添加权限
	public function rule(){
		if(Request::instance()->isPost()){
			$post = Request::instance()->post();
			//要是用户的权限
			$rule_user_group = Db::table('boke_rule_user_group')->where("uid", $post['uid'])->find();
			if(empty($rule_user_group)){
				$this->insertRule($post);
			}else{
				$post['id'] = $rule_user_group['id'];
				$this->updateRule($post);
			}

			$this->success("成功！");
		}else{
			$uid = Request::instance()->get('uid');
			//获取用户权限
			$rule_user_group = Db::table('boke_rule_user_group')->where("uid", $uid)->find();

			//获取权限列表
			$rule_ids = empty($rule_user_group['rule_ids']) ? []: json_decode($rule_user_group['rule_ids']);
			$rule = Db::table("boke_rule")->where(['status'=>['neq',111]])->select();
			foreach ($rule as $key => $vo) {
				$rule[$key]['selected'] = in_array($vo['id'], $rule_ids);
			}

			//获取权限组
			$group_ids = empty($rule_user_group['group_ids']) ? []: json_decode($rule_user_group['group_ids']);
			$rule_group = Db::table("boke_rule_group")->where(['status'=>['neq',111]])->select();
			foreach ($rule_group as $key => $vo) {
				$rule_group[$key]['selected'] = in_array($vo['id'], $group_ids);
			}

			// var_dump( $rule_group );die();
			//获取角色
			$user_group_ids = empty($rule_user_group['user_group_ids']) ? []: json_decode($rule_user_group['user_group_ids']);
			$rule_user_group = Db::table("boke_rule_user_group")->where(['status'=>['neq',111],'uid'=>0])->select();
			foreach ($rule_user_group as $key => $vo) {
				$rule_user_group[$key]['selected'] = in_array($vo['id'], $user_group_ids);
			}


			return view('rule',[
				'uid' => $uid,
				'rule'=>json_encode( $rule ),
				'rule_group'=> $rule_group,
				'rule_user_group'=> $rule_user_group
			]);			
		}
	}

	//增加菜单
	public function menu(){
		if(Request::instance()->isPost()){
			$post = Request::instance()->post();
			$user_menu = Db::table('boke_rule_user_menu')->where("uid", $post['uid'])->find();

			$menuIds = empty($post['menuIds']) ? '[]': json_encode($post['menuIds']);
			// dump( $menuIds );die();
			if(empty($user_menu)){
				//不存在记录
				(new MRuleUserMenu())->save([
					'uid' => $post['uid'],
					'menu_ids' => $menuIds,
				]);
			}else{
				//更新记录
				(new MRuleUserMenu())->save([
					'menu_ids' => $menuIds,
				],['id'=> $user_menu['id']]);
			}
			//清除用户菜单缓存
			Cache::set("userMenu_uid:{$post['uid']}",'');
			$this->success('成功');
		}else{
			$uid = Request::instance()->get('uid');
			$rule_menu = Db::table('boke_rule_menu')->where(['status'=>['neq',111]])->select();
			Loader::import('Tree',EXTEND_PATH);
			$Tree = new Tree();
			$Tree->init($rule_menu);
			$treelist = $Tree->get_tree_list(0,'');
			//获取用户菜单列表
			$MRuleUserMenu = new MRuleUserMenu();
			$userMenuIds = $MRuleUserMenu->getUserMenuIds($uid);
			foreach ($treelist as $key => $vo) {
				// dump($vo);die();
				$treelist[$key]['selected'] = in_array($vo['id'], $userMenuIds);
			}

			return view('menu',[
				'rule_menu' => $treelist,
				'uid' => $uid,
			]);			
		}
	}


	public function findOne($id){
		return MUser::find()->where("id",$id)->find();
	}


	public function insertRule($post){
		return (new MRuleusergroup())->save([
			'uid' => $post['uid'],
			'user_group_ids' => empty($post['user_group_ids']) ? '[]' : json_encode(array_values($post['user_group_ids'])),
			'group_ids' => empty($post['group_ids']) ? '[]' : json_encode(array_values($post['group_ids'])),
			'rule_ids' => empty($post['rule_ids']) ? '[]' : json_encode(array_values($post['rule_ids'])),
			'create_time' => time(),
		]);
	}

	public function updateRule($post){
		return (new MRuleusergroup())->save([
			'uid' => $post['uid'],
			'user_group_ids' => empty($post['user_group_ids']) ? '[]' : json_encode(array_values($post['user_group_ids'])),
			'group_ids' => empty($post['group_ids']) ? '[]' : json_encode(array_values($post['group_ids'])),
			'rule_ids' => empty($post['rule_ids']) ? '[]' : json_encode(array_values($post['rule_ids'])),
			'create_time' => time(),
		],['id' => $post['id']]);
	}
}
