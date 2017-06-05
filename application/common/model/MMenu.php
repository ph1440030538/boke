<?php
namespace app\common\model;

use think\Model;
use think\Db;
use think\Loader;
use extend\Tree;

class MMenu extends Model
{
	protected $table = 'boke_menu';
	protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $pageSize = 15;

    public function getAllData(){
		$where = [
			'status'=>['neq',111]
		];

    	return Db::table("boke_menu")->field("parentid,id,name,create_time,sort,url,name as label")->where($where)->order("sort asc,id asc")->select();
    }

    public function getTreeArray(){
    	$data = $this->getAllData();
			Loader::import('Tree',EXTEND_PATH);
			$Tree = new Tree();
			$Tree->init($data);
			$treelist = $Tree->get_tree_array(0,'');

			return $treelist;
    	// dump( $treelist );die;
    }

    public function getList(){
    	$data = $this->getAllData();
  		Loader::import('Tree',EXTEND_PATH);
  		$Tree = new Tree();
  		$Tree->init($data);
  		$treelist = $Tree->getTreeData(0,'');
		  return $treelist;
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
}