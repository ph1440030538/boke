<?php
namespace app\common\model;
use think\Db;
use think\Model;

class MRulegroup extends Model
{
	protected $table = "boke_rule_group";
	protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $pageSize = 15;

	public function getList($params, $page_str = 'page'){
		$curPage = isset($params['page']) && $params['page'] > 1 ? $params['page'] :1;
		$where = [
			'status'=>['neq',111]
		];

		$total = self::where($where)->count();
		$lists = self::where($where)->page("{$curPage},{$this->pageSize}")->select();
		
		foreach ($lists as $key => $vo) {
			$rule_ids = json_decode($vo['rule_ids']);
			$rule_ids = empty($rule_ids) ? [-1]:$rule_ids;
			$where = [
				'id' => ['in',$rule_ids]
			];
			$lists[$key]['rule'] = Db::table('boke_rule')->field("rule_name,id,rule")->where($where)->select();
		}
		// dump( $pageSize );die();
		return [
			'data' => $lists,
			'pages' => ceil($total/$this->pageSize),
			'curPage' => $curPage,
			'pageSize' => $this->pageSize,
      'totalRow' => $total,
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