<?php
namespace Portal\Controller;

use Common\Controller\HomebaseController;

class BookmarkController extends HomebaseController{
	public function index() {
		$bookmark_lists = $this->get_bookmark_lists();

		// dump($_SESSION);die();
		$bookmark_lists = json_encode($bookmark_lists);
		$this->assign("bookmark_lists", $bookmark_lists);
		$this->assign("is_action", $_SESSION['ADMIN_ID'] > 0 ? 1: 0);
		$this->display();
	}


	public function get_bookmark_lists(){
		$result = M('bookmark')->where(['status'=>0])->select();
		$bookmark = [];
		$data = [];
		foreach ($result as $key => $vo) {
			$bookmark[$vo['id']] = $vo;
			if($vo['parent_id']==0){
				$data[$vo['id']] = $vo;
			}
		}

		foreach ($bookmark as $key => $vo) {
			if($vo['parent_id']!=0){
				$data[$vo['parent_id']]['children'][] = $vo;
			}
		}

		$temp = [];
		foreach ($data as $key => $vo) {
			$vo['spread'] = true;
			$temp[] = $vo;
		}
		return $temp;
	}


	public function add(){
		$post = I('post.');
		$result = M('bookmark')->add($post);
		if($result){
			$this->success("添加书签成功！");
		}else{
			$this->error("添加书签失败！");
		}
	}

	public function edit(){
		$post = I('post.');
		// dump( $post );die;
		$result = M('bookmark')->where(['id'=>$post['id']])->save([
			'name' => $post['name'],
			'parent_id' => $post['parent_id'],
			'describe' => $post['describe'],
			'skip_url' => $post['skip_url'],
			'update_time' => time(),
		]);
		if($result){
			$this->success("编辑书签成功！");
		}else{
			$this->error("编辑书签失败！");
		}
	}

	public function del(){
		//http://cmf.com/portal/bookmark/edit?id=5
		$id = I('get.id');
		$result = M('bookmark')->where(['id'=>$id])->save([
			'status' =>'111',
			'update_time' => time(),
		]);
		if($result){
			$this->success("删除书签成功！");
		}else{
			$this->error("删除书签失败！");
		}

	}
}