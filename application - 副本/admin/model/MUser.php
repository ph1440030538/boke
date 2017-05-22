<?php
namespace app\admin\model;

use think\Model;
use think\Loader;
use think\Db;
use extend\Tree;
use think\Session;

class MUser extends Model
{
	protected $table = 'boke_user';
	protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

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