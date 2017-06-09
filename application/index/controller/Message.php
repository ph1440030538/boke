<?php
namespace app\index\controller;
use think\Request;
use app\common\model\MCategory;
use app\common\model\MBokearticle;

class Message
{
	protected $Model;

	public function __construct(){
		$this->Model = new MCategory();
	}

    public function index()
    {
        return view("index");
    }
}
