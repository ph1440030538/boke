<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Config;
use think\Session;
use app\admin\At\lib\Base;

class At extends Admin
{
	protected $At;

	public function __construct(){
		$this->At = new Base();
		parent::__construct();
	}

	public function index(){
		return view('index');
	}

	public function create(){
		$post = Request::instance()->post();
		try {
			//获取表的结构
			$tableInfo = $this->At->getTableInfo($post['at_table']);
		} catch (\Exception $e) {
			dump( "表不存在！" );die();
		}
// dump( $tableInfo );die();
		$fields = [];
		foreach ($tableInfo as $key => $vo) {
			if($vo['autoinc']){
				continue;
			}
			$fields[] = [
				'alias' => $vo['name'],
				'name' => $vo['comment'],
				'notnull' => $vo['notnull'],
			];
		}

		//检查文件是否存在
		$this->At->setControllerName($post['at_table']);
		$files = $this->At->checkFilesExist();
		// dump( $post );die();
		// 
		return view("create",[
			'fields' => $fields,
			'at_table' => $post['at_table'],
			'at_name' => $post['at_name'],
			'files' => $files
		]);
	}


	public function createFile(){
		$post = Request::instance()->post();
		Session::set("fileData", $post);
		$this->At->setData($post);
		

		// dump( $post );die();
		// return view("create-file",[
		// 	'files' => $files
		// ]);
	}

	public function startCreate(){
		$filename = Request::instance()->post("filename");
		$fileData = Session::get("fileData");
		$this->At->setData($fileData);

		if($filename == 'all'){
			$this->At->createFile('controller');
			$this->At->createFile('model');
			$this->At->createFile('validate');
			$this->At->createFile('index');
			$this->At->createFile('add');
			$this->At->createFile('edit');
		}else{
			$this->At->createFile($filename);
		}
		$this->success("生成成功！");
	}

	public function createInit(){
		$this->doCreateController();
		$this->doCreateModel();
		$this->doCreateValidate();
		$this->doCreateHtml();
	}


	public function createModel(){
		return view("create-model");
	}


	//执行动作
	/*创建控制器文件*/
	public function doCreateController(){
		$this->At->doCreateController();
	}

	/*创建Model文件*/
	public function doCreateModel(){
		$this->At->doCreateModel();
	}

	/*创建Validate文件*/
	public function doCreateValidate(){
		$this->At->doCreateValidate();
	}

	/*创建Html文件*/
	public function doCreateHtml(){

		/***********index**********/
		$this->At->doCreateIndexHtml();
		/***********add**********/
		$this->At->doCreateAddHtml();
		/***********edit**********/
		$this->At->doCreateEditHtml();
	}

}
