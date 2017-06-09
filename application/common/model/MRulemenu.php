<?php
namespace app\common\model;

use think\Model;
use think\Db;
use think\Cache;
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

    	return Db::table("boke_rule_menu")->field("parentid,id,name,sort,name as label")->where($where)->select();
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

    public function getElOption(){
      $data = $this->getAllData();

      Loader::import('Tree',EXTEND_PATH);
      $Tree = new Tree();
      $Tree->init($data);
      $treelist = $Tree->getElOption(0);
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
    			'href' => "{$vo['url']}",
    			'parentid' => $vo['parentid'],
    			'id' => $vo['id'],
    		];
    		$roleHref[] = strtolower("{$vo['url']}");
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

	//获取用户菜单
	public function getUserMenu($uid){
    	$userMenu = Cache::get("userMenu_uid:{$uid}");
    	if(!empty($userMenu)){
    		return $userMenu;
    	}

      //获取用户菜单
      $MRuleUserMenu = new MRuleUserMenu();
      $userMenuIds = $MRuleUserMenu->getUserMenuIds($uid);
      $where = [
          'status'=>['neq',111],
      ];

      $access_admin_id = Session::get('access_admin_id');
      if(!in_array($uid, $access_admin_id)){
        if(empty($userMenuIds)){
          return [];
        }
        $where['id'] = ['in', $userMenuIds];
      }

      $ruleMenu = self::where($where)->order("sort asc")->select();
      $ruleMenu = $this->getMenu($ruleMenu);

      Cache::set("userMenu_uid:{$uid}",$ruleMenu);
      return $ruleMenu;
	}
}