<?php
namespace app\index\controller;
use app\common\model\MBokearticle;
use think\Request;

class Index
{

    public function index()
    {
        $list = (new MBokearticle())->getList(['category_id'=>9,'page'=>Request::instance()->get('page',1)]);

        return view('index',['list'=> $list]);
    }
}
