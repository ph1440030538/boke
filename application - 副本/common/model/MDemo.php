<?php
namespace app\common\model;

use think\Model;

class MDemo extends Model
{
	protected $table = "boke_demo";
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
}