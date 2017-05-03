<?php
namespace Portal\Controller;

use Common\Controller\HomebaseController;

class MessageController extends HomebaseController{
	public function index() {
		$comments = M('guestbook')->order('RAND()')->limit(30)->getField('msg',true);

		$this->assign("comments",json_encode($comments));
		$this->display();
	}

	public function send(){
		$content = I('post.content');
		M('guestbook')->add(['msg'=>$content]);
		$this->ajaxReturn(['status'=>200]);
	}

}