<?php
namespace app\admin\model;

use think\Model;
use think\Loader;
use think\Db;
use extend\Tree;


class MCategory extends Model
{
	protected $table = 'boke_category';
	protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    public function getAllData(){
		$where = [
			'status'=>['neq',111]
		];

    	return Db::table("boke_category")->field("parent_id as parentid,id,name,create_time,sort")->where($where)->select();
    }


    public function getList(){
    	$data = $this->getAllData();
		Loader::import('Tree',EXTEND_PATH);
		$Tree = new Tree();
		$Tree->init($data);
		$treelist = $Tree->get_tree_list(0);

		return $treelist;
    }

    public function get_child($id){
    	$data = $this->getAllData();
		Loader::import('Tree',EXTEND_PATH);
		$Tree = new Tree();
		$Tree->init($data);
		$treelist = $Tree->get_child($id);
		if(!empty($treelist)){
			$ids = array_column($treelist, "id");
		}
		
		$ids[] = $id;
		return $ids;
    }

    public function getOption($select_id = -1){
    	$data = $this->getAllData();

		Loader::import('Tree',EXTEND_PATH);
		$Tree = new Tree();
		$Tree->init($data);
		$str = "<option value=\$id \$selected>\$spacer\$name</option>"; //生成的形式
		$treelist = $Tree->get_tree(0,$str, $select_id);
		return $treelist;
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

	//列表模板
	public function getListTpl(){
		return ['list'];
	}

	//文章模板
	public function getPageTpl(){
		return ['page'];
	}

}