<?php
namespace app\admin\controller;
use think\Cache;

class Main extends Base
{
    public function index()
    {
    	$this->view->engine->layout(false);
        return view('index');
    }

    public function main(){
    	
    }

    public function flush(){
        Cache::clear(); 
        return json(['status'=>200]);
    }
}
