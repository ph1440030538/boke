<?php
namespace Portal\Controller;
use Common\Controller\HomebaseController; 
/**
 * 首页
 */
class IndexController extends HomebaseController {
	
	public function index() {
		$category_id = I('get.category_id',0);
		$is_show_img = 1;
		if($category_id == 0){
			$category_id = "5,6,7";
		}else{
			$is_show_img = 0;
		}
		$keyword = I('get.keyword');
		if(empty($keyword)){
			$lists = sp_sql_posts_paged("cid:$category_id;order:post_date DESC;",10);
		}else{
			$lists = sp_sql_posts_paged("cid:$category_id;order:post_date DESC;where:posts.post_title like '%{$keyword}%'",10);
		}

		$this->assign("category_id", $category_id);
		$this->assign("keyword", $keyword);
		$this->assign("is_show_img", $is_show_img);
		$this->assign("lists", $lists);
    	$this->display(":index");
    }

}


