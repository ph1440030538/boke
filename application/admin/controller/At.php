<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Config;
use app\admin\At\lib\Base;

class At extends Controller
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


	public function doing(){
		$post = Request::instance()->post();
		$fields = $this->At->createFields($post);

		$this->createInit();
		// dump( 'success' );die();
	}

	public function createInit(){
		$this->createController();
		$this->createModel();
		$this->createValidate();
		$this->createHtml();
	}

	/*创建控制器文件*/
	public function createController(){
		$this->At->createController();
	}

	/*创建Model文件*/
	public function createModel(){
		$this->At->createModel();
	}

	/*创建Validate文件*/
	public function createValidate(){
		$this->At->createValidate();
	}

	/*创建Html文件*/
	public function createHtml(){

		/***********index**********/
		$this->At->createIndexHtml();
		/***********add**********/
		$this->At->createAddHtml();
		/***********edit**********/
		$this->At->createEditHtml();
	}

}
