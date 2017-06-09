<?php
namespace app\index\controller;
use think\Request;
use app\common\model\MCategory;
use app\common\model\MBokearticle;
use think\Controller;

class Category extends Controller
{
	protected $Model;

	public function __construct(){
		$this->Model = new MCategory();
	}

    public function index($id = 0)
    {
    	$category = $this->Model->getDataById($id);
        if(empty($category)){
            $this->error("找不到该分类");
        }
        $list = (new MBokearticle())->getList(['category_id'=>$category['id'],'page'=>Request::instance()->get('page',1)]);

        return view("\\list\\".$category['list_tpl'],['category'=>$category,'list'=>$list]);
    }
}
