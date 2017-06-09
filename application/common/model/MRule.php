<?php
namespace app\common\model;

use think\Model;
use think\Db;
use think\Loader;
use extend\Tree;
use think\Session;
use think\Cache;

class MRule extends Model
{
	protected $table = 'boke_rule';
	protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $pageSize = 12;
    protected $rule_user_group = [];
    protected $group_ids = [];
    protected $rule_ids = [];

    public function getList($params, $page_str = 'page'){
        $curPage = isset($params['page']) && $params['page'] > 1 ? $params['page'] :1;
        $where = [
            'status'=>['neq',111]
        ];

        isset($params['rule_name'])&&$where['rule_name']=['like',"%".$params['rule_name']."%"];
        isset($params['rule'])&&$where['rule']=['like',"%".$params['rule']."%"];


        $total = self::where($where)->count();
        $lists = self::where($where)->page("{$curPage},{$this->pageSize}")->order("id desc")->select();
        return [
            'data' => $lists,
            'pages' => ceil($total/$this->pageSize),
            'curPage' => $curPage,
            'totalRow' => $total,
            'pageSize' => $this->pageSize
        ];
    }

    /**
     * 获取用户权限所有的节点
     */
    public function getRuleInfoByUid($uid){
    	$RuleInfo = Cache::get("RuleInfo_uid:{$uid}");
    	if(!empty($RuleInfo)){
    		return $RuleInfo;
    	}

    	//查询权限用户表
    	$this->rule_user_group = Db::table("boke_rule_user_group")->where(['uid'=>$uid])->select();
    	foreach ($this->rule_user_group as $key => $rule_user_group) {
	    	$user_group_ids = json_decode($rule_user_group['user_group_ids']);

	    	$this->group_ids  = array_merge($this->group_ids, json_decode($rule_user_group['group_ids']));
	    	$this->rule_ids  = array_merge($this->rule_ids, json_decode($rule_user_group['rule_ids']));

	    	if(!empty($user_group_ids)){
	    		$this->getChildRuleUserGroup($user_group_ids);
	    	}
    	}
    	 // dump( $uid );die();
    	//查询权限组表
    	$group_ids = array_unique($this->group_ids);
        $group_ids = empty($group_ids) ? -1:$group_ids;
    	$user_group_data = Db::table("boke_rule_group")->where([
    		'id' => ['in', $group_ids],
    	])->select();
       
    	foreach ($user_group_data as $key => $user_group) {
    		$this->rule_ids = array_merge($this->rule_ids, json_decode($user_group['rule_ids']));
    	}
    	//查询权限节点表
    	$rule_ids = array_unique($this->rule_ids);
        $rule_ids = empty($rule_ids) ? -1:$rule_ids;
    	$rule_data = Db::table("boke_rule")->where([
    		'id' => ['in', $rule_ids]
    	])->select();

    	$rule = [];
    	foreach ($rule_data as $key => $vo) {
    		if(empty($vo['params'])){
	    		$rule[$vo['rule']]['rule'] = $vo['rule'];
	    		$rule[$vo['rule']]['isAccept'] = $vo['isAccept'];    			
    		}


    		if(!empty($vo['params'])){
    			$params = explode("&", $vo['params']);
    			$rule_params = [];
    			foreach ($params as $key => $param) {
    				$temp = explode("=", $param);
    				$rule_params[$temp[0]] = $temp[1];
    			}
    			// dump($rule_params);die();
    			$rule_params['isAccept'] = $vo['isAccept'];
    			$rule[$vo['rule']]['params'][] = $rule_params;
    		}
    	}

    	Cache::set("RuleInfo_uid:{$uid}",$rule);
    	return $rule;
    }

    /**
     * 获取用户子用户组
     */
    private function getChildRuleUserGroup($ids){
    	if(empty($ids)){
    		return [];
    	}
    	$where = [
    		'id' => ['in',$ids]
    	];
    	$rule_user_group = Db::table("boke_rule_user_group")->where($where)->select();
    	foreach ($rule_user_group as $key => $vo) {
    		$child_ids = json_decode($vo['user_group_ids']);
    		$this->rule_user_group[] = $vo;
	    	$this->group_ids  = array_merge($this->group_ids, json_decode($vo['group_ids']));
	    	$this->rule_ids  = array_merge($this->rule_ids, json_decode($vo['rule_ids']));

    		if(!empty($child_ids)){
    			$this->getChildRuleUserGroup($child_ids);
    		}
    	}
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