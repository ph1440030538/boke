<?php
/**
 * Menu(菜单管理)
 */
namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class ResourceController extends AdminbaseController {

    public function _initialize() {
        parent::_initialize();
    }

    // 后台菜单列表
    public function index() {
        $where['status'] = 0;
        if($_POST){
            !empty($_POST['title'])&& $where['title'] = ['like',"%{$_POST['title']}%"];
        }

        $query = M('resource')->where($where);
        
        $count = $query->count();
        $Page = new \Think\Page($count,15);
        $show = $Page->show();

        $lists = M('resource')->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();

        $this->assign("lists",$lists);
        $this->assign('page',$show);
        $this->display();
    }


    public function add(){
        $this->display();
    }

    public function add_post(){
        if (IS_POST) {
            $post = I('post.');
            $data = [
                'title' => $post['title'],
                'file_addr' => $post['file_addr'],
                'file_size' => $post['file_size'],
                'file_name' => $post['file_name'],
                'image_url' => $post['image_url'],
                'content' => htmlspecialchars_decode($post['content']),
                'post_source' => $post['post_source'],
                'create_time' => time(),
            ];
            $result = M('resource')->add($data);
            if ($result) {
                $this->success("添加成功！");
            } else {
                $this->error("添加失败！");
            }
        }
    }


    public function edit(){
        $id = I('get.id');
        $data = M('resource')->where(['id'=>$id])->find();
        $this->assign($data);
        $this->assign("id",$id);
        $this->display();
    }


    public function edit_post(){
        if (IS_POST) {
            $post = I('post.');
            $data = [
                'title' => $post['title'],
                'file_addr' => $post['file_addr'],
                'file_size' => $post['file_size'],
                'file_name' => $post['file_name'],
                'image_url' => $post['image_url'],
                'content' => htmlspecialchars_decode($post['content']),
                'post_source' => $post['post_source'],
                'create_time' => time(),
            ];
            // dump($data);die();
            $result = M('resource')->where(['id'=>$post['id']])->save($data);
            if ($result) {
                $this->success("修改成功！");
            } else {
                $this->error("修改失败！");
            }
        }
    }

    public function delete(){
        $id = I('get.id');
        $result = M('resource')->where(['id'=>$id])->save(['status'=>111]);
        if ($result) {
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        }
    }
}
