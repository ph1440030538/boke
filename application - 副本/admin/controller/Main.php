<?php
namespace app\admin\controller;
use think\Cache;
use think\Session;

class Main extends Admin
{
    public function index()
    {
    	$this->view->engine->layout(false);

        return view('index',[
            'roleMenu'=>Session::get("ruleMenu")]);
    }

    public function main(){
    	
    }

    public function flush(){
        Cache::clear(); 
        return json(['status'=>200]);
    }
}
