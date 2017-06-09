<?php
namespace app\index\controller;
use think\Request;
use app\common\model\MCategory;
use app\common\model\MBokearticle;

class Tools
{
	protected $Model;

	public function __construct(){
	}

    public function index()
    {
        return view("index");
    }
}
