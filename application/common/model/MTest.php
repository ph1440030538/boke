<?php
namespace app\common\model;

use think\Model;

class MTest extends Model
{
    protected $pageSize = 15;
	protected $table = "boke_test";
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
			'page' => [
				'pageSize' => $this->pageSize,
				'total' => $total,
				'curPage' => $curPage
			]
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