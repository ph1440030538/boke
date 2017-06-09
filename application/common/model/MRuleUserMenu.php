<?php
namespace app\common\model;

use think\Model;

class MRuleUserMenu extends Model
{
	protected $table = "boke_rule_user_menu";
	protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $pageSize = 15;

    public function getUserMenuIds($uid){
    	$where = [
    		'uid' => $uid,
    	];
    	$userMenu = self::where($where)->find();
    	$userMenuIds = empty($userMenu['menu_ids']) ? [] : json_decode($userMenu['menu_ids'], true);
    	// var_dump( $userMenuIds );die();
    	return $userMenuIds;
    }
}