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
			$tableInfo = $this->At->getTableInfo($post['tableName']);
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
		// dump( $post );die();
		// 
		return view("create",[
			'fields' => $fields,
			'tableName' => $post['tableName'],
			'title' => $post['title'],
		]);
	}


	public function createFile(){
		$post = Request::instance()->post();
		Session::set("fileData", $post);
		$post['typeFile'] = 00;
		switch ($post['typeFile']) {
			case 'createModel':
				$this->At->setData($post);
				$this->At->isFileExist();
				break;
			default:
				$this->At->setData($post);
				$this->At->setField($post);
				break;
		}
		
		// var_dump( $file );die();
		return view("create-file", [
			'file' => $file
		]);
	}

	public function startCreate(){
		$fileData = Session::get("fileData");
		switch ($fileData['typeFile']) {
			case 'createModel':
				$this->At->setData($fileData);
				$this->doCreateModel();
				$this->success("成功");
				break;
			default:
		}
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
