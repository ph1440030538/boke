<?php
namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class PageController extends AdminbaseController{
	protected $page;
	protected $terms_model;

	public function _initialize() {
		parent::_initialize();
		$this->page_model = D("Common/Page");
		$this->terms_model = D("Portal/Terms");
	}
	
	public function index(){
		$count      = $this->page_model->where('status=1')->count();
		$Page       = new \Think\Page($count,20);
		$show       = $Page->show();

		$list = $this->page_model->where('status=1')->order('create_time')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display();
	}

	public function add(){
		if(IS_POST){
    		if ($this->page_model->create()!==false) {
    			if ($this->page_model->add()!==false) {
    				$this->success("成功", U('page/index'));
    			}else{
    				$this->success("失败");
    			}
    		}else{
    			$this->error($this->page_model->getError());
    		}
		}else{
			$terms = $this->terms_model->order(array("listorder"=>"asc"))->select();
			// var_dump($terms);die;
			//获取页面列表文件
			$pages = $this->page_model->getPages();

			$this->assign("pages", $pages);
			$this->assign("terms", $terms);
			$this->display();
		}
	}

	public function edit(){
		if(IS_POST){
    		if ($this->page_model->create()!==false) {
    			if ($this->page_model->save()!==false) {
    				$this->success("成功", U('page/index'));
    			}else{
    				$this->success("失败");
    			}
    		}else{
    			$this->error($this->page_model->getError());
    		}
		}else{
			$id = I('get.id');
			$data = $this->page_model->where("id=%d",[$id])->find();
			$terms = $this->terms_model->order(array("listorder"=>"asc"))->select();
			//获取页面列表文件
			$pages = $this->page_model->getPages();

			$this->assign("pages", $pages);
			$this->assign("terms", $terms);
			$this->assign("data", $data);
			$this->display();
		}
	}

}