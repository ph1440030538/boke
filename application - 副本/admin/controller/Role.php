<?php
/**
 * roleName
 */
namespace app\admin\controller;
use think\Request;
use think\Db;
use app\common\model\MRole;
use app\common\model\MRolemenu;
use app\common\model\MRoleaccess;
use app\common\model\MRoleuser;

class Role extends Admin
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

	//获取菜单
	public function getMenu(){
		$MRolemenu = new MRolemenu();
		$data = $MRolemenu->getMenu();
		return json($data);
	}


	//增加用户
	public function adduser(){
		if(Request::instance()->isPost()){
			$data = Request::instance()->post();
			$submitData = empty($data['username']) ? []: array_keys($data['username']); 
			$oldData = Db::table("boke_role_user")->where(['role_id'=>$data['role_id']])->select();
			$oldData = array_column($oldData, 'uid');
			//var_dump( $oldData );die();
			$addData = array_diff($submitData, $oldData);
			$deleteData = array_diff($oldData, $submitData);
			$deleteData = empty($deleteData) ? '-1' : $deleteData;

			$list = [];
			foreach ($addData as $key => $uid) {
				$list[] = [
					'role_id' => $data['role_id'],
					'uid' => $uid,
				];
			}
			$MRoleuser = new MRoleuser();
			$MRoleuser->saveAll($list);



			$MRoleuser->where([
				'role_id' => ['=',$data['role_id']],
				'uid' => ['in', $deleteData]
			])->delete() ;

			// var_dump( $result );die();

		}else{
			$role_id = Request::instance()->get("id");
			$where = [
				'status'=>['neq',111]
			];
			$users = Db::table("boke_user")->where($where)->order("id desc")->select();

			$MRoleuser = new MRoleuser();
			$roleuserData = $MRoleuser->where([
				'role_id' => ['=',$role_id],
			])->select();
			$roleuser = [];
			foreach ($roleuserData as $key => $vo) {
				$roleuser[] = $vo['uid'];
			}
			// $roleuser = array_column($roleuser, "uid");
// var_dump( $roleuser );die();

			return view('adduser',[ 'users' => $users,'role_id'=>$role_id,'roleuser'=>$roleuser ]);			
		}
	}

	//增加用户
	public function auth(){
		if(Request::instance()->isPost()){
			$data = Request::instance()->post();
			//获取旧的数据
			$where = [
				'role_id' => $data['role_id']
			];
			$oldRoleAccess = Db::table("boke_role_access")->where($where)->select();
			$oldRoleAccess = array_column($oldRoleAccess, "menu_id");
			$addRoleAccess = array_diff($data['id'], $oldRoleAccess);
			$deleteRoleAccess = array_diff($oldRoleAccess, $data['id']);
			$deleteRoleAccess = empty($deleteRoleAccess) ? '-1' : $deleteRoleAccess;
			$list = [];
			foreach ($addRoleAccess as $key => $menu_id) {
				$list[] = [
					'role_id' => $data['role_id'],
					'menu_id' => $menu_id,
				];
			}

			$MRoleaccess = new MRoleaccess();
			$result = $MRoleaccess->saveAll($list);

			$MRoleaccess->where([
				'role_id' => ['=',$data['role_id']],
				'menu_id' => ['in', $deleteRoleAccess]
			])->delete() ;


			if(false === $result){
			    // 验证失败 输出错误信息
			    $this->error($this->Model->getError());
			}else{
				$this->success("成功！");
			}
		}else{
			$id = Request::instance()->get('id/d');
			$MRolemenu = new MRolemenu();
			$auth = $MRolemenu->getMenu();

			$where = [
				'role_id' => $id
			];
			$accessAuth = Db::table("boke_role_access")->where($where)->select();
			$accessAuth = array_column($accessAuth, "menu_id");

			// var_dump( $accessAuth );die();
			return view('auth',[ 'auth' => json_encode($auth) ,'role_id'=>$id,'accessAuth'=>json_encode($accessAuth)]);
		}
	}


	public function findOne($id){
		return MRole::find()->where("id",$id)->find();
	}
}
