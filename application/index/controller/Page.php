<?php
namespace app\index\controller;
use think\Request;
use app\common\model\MBokearticle;
use app\common\model\MCategory;

class Page
{
	protected $Model;

	public function __construct(){
		$this->Model = new MBokearticle();
	}

    public function index($id = 0)
    {
    	$model = $this->Model->getDataById($id);
    	$category = (new MCategory)->getDataById($model['category_id']);

        return view("\\page\\".$category['article_tpl'],['model'=>$model,'category'=>$category]);
    }
}
