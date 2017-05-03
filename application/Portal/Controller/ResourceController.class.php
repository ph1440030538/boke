<?php
namespace Portal\Controller;
use Common\Controller\HomebaseController; 

class ResourceController extends HomebaseController {
	
	public function index() {
        $query = M('resource');
        
        $count = $query->where(['status'=>0])->count();
        $Page = new \Think\Page($count,15);
        $show = $Page->show();

        $lists = $query->where(['status'=>0])->limit($Page->firstRow.','.$Page->listRows)->select();

        $this->assign("lists",$lists);
        $this->assign('page',$show);
        $this->display();
    }

    public function detail(){
    	$id = I('get.id');
        $data = M('resource')->where(['id'=>$id])->find();

        $this->assign($data);
        $this->display();
    }

}


