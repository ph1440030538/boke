<?php
namespace app\common\model;

use think\Model;

class MBokearticle extends Model
{
	protected $table = 'boke_article';
	protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $pageSize = 12;

	public function getList($params, $page_str = 'page'){
		$curPage = isset($params['page']) && $params['page'] > 1 ? $params['page'] :1;
		$where = [
			'status'=>['neq',111]
		];

		isset($params['category_id'])&&$params['category_id']!=-1&&$where['category_id']=$params['category_id'];
		isset($params['title'])&&$where['title']=['like',"%".$params['title']."%"];

		$total = self::where($where)->count();
		$lists = self::where($where)->page("{$curPage},{$this->pageSize}")->select();
		
		return [
			'data' => $lists,
			'pages' => ceil($total/$this->pageSize),
      'curPage' => $curPage,
      'totalRow' => $total,
      'pageSize' => $this->pageSize
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

	public function getDataById($id){
		return self::where(['id'=>$id])->find();
	}
}