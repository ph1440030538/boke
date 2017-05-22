<?php
namespace app\common\model;
use think\Session;
use think\Model;

class MUser extends Model
{
    protected $pageSize = 15;
	protected $table = "boke_user";
	protected $autoWriteTimestamp = true;
	protected $createTime = 'create_time';
	protected $updateTime = 'update_time';

	public function getList($params, $page_str = 'page'){
		$curPage = isset($params['page']) && $params['page'] > 1 ? $params['page'] :1;
		$where = [
			'status'=>['neq',111]
		];

		$total = self::where($where)->count();
		$lists = self::where($where)->page("{$curPage},{$this->pageSize}")->select();
		
		return [
			'data' => $lists,
			'pages' => ceil($total/$this->pageSize),
			'curPage' => $curPage,
		];
	}

	public function deleteAll($id){
		if(!is_array($id)){
			$id = [$id];
		}
		return self::where("id","in",$id)->update([
			'status' => 111,
			'update_time' => time()
		]);
	}


    public function isCheckLogin($data){
    	$user = $this->getUserByUserName($data['username']);
    	if($user['password'] == $this->encryption($data['password'])){
    		Session::set("user",[
    			'username' => $user['username'],
    			'id' => $user['id'],
    		]);
    		return $user;
    	}
    	return false;
    }

    public function getUserByUserName($username){
    	return self::where(['username'=>$username])->find();
    }

    public function encryption($password){
    	return md5($password.sha1($password)."boke");
    }
}