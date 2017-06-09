<?php
namespace app\common\model;

use think\Model;
use think\Db;

class MRuleusergroup extends Model
{
	protected $table = "boke_rule_user_group";
	protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $pageSize = 15;

	public function getList($params, $page_str = 'page'){
		$curPage = isset($params['page']) && $params['page'] > 1 ? $params['page'] :1;
		$where = [
			'status'=>['neq',111],
			'uid' => 0,
		];

		$total = self::where($where)->count();
		$lists = self::where($where)->page("{$curPage},{$this->pageSize}")->select();
// dump( $lists );die();

		//权限名字
		$user_group_ids = [];
		$group_ids = [];
		$rule_ids = [];
		foreach ($lists as $key => $vo) {
			$user_group_ids = array_merge($user_group_ids, json_decode($vo['user_group_ids']) );
			$group_ids = array_merge($group_ids, json_decode($vo['group_ids']) );
			$rule_ids = array_merge($rule_ids, json_decode($vo['rule_ids']) );
		}
		$user_group_ids = empty($user_group_ids) ? [-1] : array_unique($user_group_ids);
		$group_ids = empty($group_ids) ? [-1] : array_unique($group_ids);
		$rule_ids = empty($rule_ids) ? [-1] : array_unique($rule_ids);
		$user_group = Db::table("boke_rule_user_group")->where([
			'id' => ['in', $user_group_ids],
			'status' => 0,
		])->select();
		$user_group = array_column($user_group, "user_group_name", "id");
		$group = Db::table("boke_rule_group")->where([
			'id' => ['in', $group_ids],
			'status' => 0,
		])->select();
		$group = array_column($group, "group_name", "id");
		$rule = Db::table("boke_rule")->where([
			'id' => ['in', $rule_ids],
			'status' => 0,
		])->select();
		$rule = array_column($rule, "rule_name", "id");

		return [
			'data' => $lists,
			'pages' => ceil($total/$this->pageSize),
      'curPage' => $curPage,
      'totalRow' => $total,
      'pageSize' => $this->pageSize,
			'user_group' => $user_group,
			'group' => $group,
			'rule' => $rule,
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
}