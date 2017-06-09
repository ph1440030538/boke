<?php
namespace app\index\controller;
use think\Request;
use think\Controller;
use app\common\model\MLostPeople;

class People extends Controller
{
	protected $Model;

	public function __construct(){
		$this->Model = new MLostPeople();
	}

	public function index(){
		//获取寻人列表
		$list = $this->Model->getLostProple();

		// var_dump( $list );die();
		return view("index",[
			'list' => $list,
		]);
	}

	public function detail()
    {
    	$id = Request::instance()->param('id');
    	$lostProple = $this->Model->getLostPropleById($id);
    	$lostProple['images'] = json_decode($lostProple['images']);
    	// var_dump( $lostProple );die();
        return view("detail",[
        	'lostProple' => $lostProple,
        ]);
    }


    public function release()
    {
        return view("release");
    }

    public function dorelease(){
    	$data = Request::instance()->post();
    	$data['images'] = empty($data['images']) ? '[]' : json_encode(array_values($data['images']));
		$result = $this->Model->validate(true)->save($data);

		if(false === $result){
		    // 验证失败 输出错误信息
		    $this->error($this->Model->getError());
		}else{
			$this->success("成功！");
		}

    	var_dump( $data );
    }
}
