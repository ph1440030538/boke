<?php
namespace app\common\model;

use think\Model;
use think\Db;
use think\Loader;
use think\Session;
use extend\Tree;

class MRulemenu extends Model
{
	protected $table = "boke_rule_menu";
    protected $pageSize = 15;

    public function getAllData(){
		$where = [
			'status'=>['neq',111]
		];

    	return Db::table("boke_rule_menu")->field("parentid,id,name,sort")->where($where)->select();
    }

    public function getList(){
    	$data = $this->getAllData();
		Loader::import('Tree',EXTEND_PATH);
		$Tree = new Tree();
		$Tree->init($data);
		$treelist = $Tree->get_tree_array_2(0,'');
		// dump( $treelist );die();
    	// exit( json_encode( $treelist  ));die();
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

    public function getMenu($sqlData = []){
    	if(empty($sqlData)){
			$where = [
				'status'=>['neq',111]
			];

	    	$sqlData = Db::table("boke_rule_menu")->field("parentid,id,name,sort,icon,app,model,action")->where($where)->order("sort asc")->select();
    	}


    	$data = [];
    	$roleHref = [];
    	foreach ($sqlData as $key => $vo) {
    		$data[] = [
    			'title' => $vo['name'],
    			'icon' => $vo['icon'],
    			'href' => "/{$vo['app']}/{$vo['model']}/{$vo['action']}",
    			'parentid' => $vo['parentid'],
    			'id' => $vo['id'],
    		];
    		$roleHref[] = strtolower("/{$vo['app']}/{$vo['model']}/{$vo['action']}");
    	}
    	Session::set('roleHref',$roleHref);

		Loader::import('Tree',EXTEND_PATH);
		$Tree = new Tree();
		$Tree->init($data);
		$treelist = $Tree->get_tree_array_3(0,'');
		return $treelist;
		// dump( $treelist );die();
    }

	public function deleteAll($id){
		if(!is_array($id)){
			$id = [$id];
		}
		return self::where("id","in",$id)->update([
			'status' => 111
		]);
	}
}