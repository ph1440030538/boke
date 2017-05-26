<?php
namespace app\index\controller;
use app\common\model\MBokearticle;
use think\Request;
use think\Controller;

class Index extends Controller
{

    public function index()
    {
    	$this->redirect('/index/people/index');

        $list = (new MBokearticle())->getList(['category_id'=>9,'page'=>Request::instance()->get('page',1)]);

        return view('index',['list'=> $list]);
    }
}
