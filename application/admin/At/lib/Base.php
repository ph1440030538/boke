<?php
namespace app\admin\At\lib;
use think\Db;
use think\Config;


class Base{
	protected $title = "";
	protected $ControllerName = '';
	protected $tableName = '';
	protected $fields = [];
	protected $create_time = "";
	protected $update_time = "";
	protected $isAutoTime;

	protected $fileSavePath = [
		"controller" => "../application/admin/controller/",
		"model" => "../application/common/model/",
		"validate" => "../application/common/validate/",
		"html" => "../application/admin/view/",
	];

	public function __construct(){
	}

	//设置数据
	public function setData($post){
		$this->create_time = isset($post['create_time']) ? $post['create_time'] : '';
		$this->update_time = isset($post['update_time']) ? $post['update_time'] : '';

		//控制器名字
		$this->ControllerName = ucfirst( str_replace(Config::get('database.prefix'), '', $post['at_table']) );
		$this->ControllerName = str_replace("_", "", $this->ControllerName);
		//功能名字
		$this->title = isset($post['at_name']) ? $post['at_name'] : '';
		//表名
		$this->tableName = isset($post['at_table']) ? $post['at_table'] : '';

		$this->setField($post);

		//是否时间戳
		$this->isAutoTime = isset($post['open_time']);
	}

	//获取文件列表
	public function getFileList(){
		
	}

	public function setControllerName($at_table){
		$this->ControllerName = ucfirst( str_replace(Config::get('database.prefix'), '', $at_table) );
	}


	public function setField($post){
		$tableInfo = $this->getTableInfo($post['at_table']);
		foreach ($tableInfo as $key => $field) {
			if($field['autoinc']){
				continue;
			}
			$default = "";
			if( $post['type'][$field['name']] == 'select'){
				$defaultData = $post['default'][$field['name']];
				$defaultData = explode(",",$defaultData);
				$default = [];
				foreach ($defaultData as $key => $vo) {
					$data = explode("=>",$vo);
					$default[] = [
						'value' => $data['0'],
						'name' => $data['1']
					];
				}
			}

			preg_match('/(\w+)\((\d+)\)/i',$tableInfo[$field['name']]['type'],$match);
// dump( $field['name']);dump( $match );
			$fields[$field['name']] = [
				'alias' => $field['name'],
				'name' => $post['name'][$field['name']],
				'type' => $post['type'][$field['name']],
				'datatype' => empty($match[1]) ? '':$match[1],
				'datalength' => empty($match[2]) ? '':$match[2],
				'notnull' => isset($post['notnull'][$field['name']])&& $post['notnull'][$field['name']] == 1? 1:0,
				'is_list' => isset($post['is_list'][$field['name']])&& $post['is_list'][$field['name']] == 1? 1:0,
				'is_edit' => isset($post['is_edit'][$field['name']])&& $post['is_edit'][$field['name']] == 1? 1:0,
				'default' => $default,
			];
		}

		$this->fields = $fields;

	}

	//预览文件
	public function previewFile($fileType){
		$action = "\$FileContent = \$this->get".ucfirst($fileType)."File();";
		// dump( $action );die();
		eval($action);
		return $FileContent;
	}

	//生成文件
	public function createFile($fileType){

		if(!in_array($fileType , ['controller','model','validate','add','edit','index'])){
			exit('找不到类型');
		}else{
			$FileContent = $this->previewFile($fileType);

			switch ($fileType) {
				case 'controller':
					$path = $this->fileSavePath['controller'];
					$this->checkPath($path);
					return file_put_contents($path."{$this->ControllerName}.php", $FileContent);
					break;
				case 'model':
					$path = $this->fileSavePath['model'];
					$this->checkPath($path);
					return file_put_contents($path."M{$this->ControllerName}.php", $FileContent);
					break;
				case 'validate':
					$path = $this->fileSavePath['validate'];
					$this->checkPath($path);
					return file_put_contents($path."M{$this->ControllerName}.php", $FileContent);
					break;
				case 'index':
					$path = $this->fileSavePath['html'].strtolower($this->ControllerName)."/";
					$this->checkPath($path);
					return file_put_contents($path."index.html", $FileContent);
					break;
				case 'add':
					$path = $this->fileSavePath['html'].strtolower($this->ControllerName)."/";
					$this->checkPath($path);
					return file_put_contents($path."add.html", $FileContent);
					break;
				case 'edit':
					$path = $this->fileSavePath['html'].strtolower($this->ControllerName)."/";
					$this->checkPath($path);
					return file_put_contents($path."edit.html", $FileContent);
					break;
			}
		}
		return false;
	}


	public function checkFilesExist(){

		return [
			'controller' => $this->checkFileExist($this->fileSavePath['controller'].$this->ControllerName.".php"),
			'model' => $this->checkFileExist($this->fileSavePath['model']."M".$this->ControllerName.".php"),
			'validate' => $this->checkFileExist($this->fileSavePath['validate']."M".$this->ControllerName.".php"),
			'add' => $this->checkFileExist($this->fileSavePath['html'].strtolower($this->ControllerName)."/add.html"),
			'edit' => $this->checkFileExist($this->fileSavePath['html'].strtolower($this->ControllerName)."/edit.html"),
			'index' => $this->checkFileExist($this->fileSavePath['html'].strtolower($this->ControllerName)."/index.html"),
		];
	}

	//判断文件是否存在
	public function checkFileExist($file){
		$isExist = file_exists($file);

		return [
			'isExist' => $isExist,
			'filename' => $file
		];
	}


	/*创建控制器文件*/
	public function getControllerFile(){
		$FileContent = $this->readFile("Controller.php");

		//替换
		$FileContent = str_replace("【ControllerName】",$this->ControllerName, $FileContent);

		//写入文件
		return $FileContent;
	}

	/*创建Model文件*/
	public function getModelFile(){
		$FileContent = $this->readFile("Model.php");

		//替换自动时间戳
		if($this->isAutoTime){
			$FileContent = str_replace("【autoWriteTimestamp】",
				"protected \$autoWriteTimestamp = true;".
				"\n\tprotected \$createTime = '{$this->create_time}';".
				"\n\tprotected \$updateTime = '{$this->update_time}';"
				, 
			$FileContent);
		}else{
			$FileContent = str_replace("【autoWriteTimestamp】","", $FileContent);
		}

		// var_dump( $FileContent );die();
		//替换
		$FileContent = str_replace("【ControllerName】",$this->ControllerName, $FileContent);
		$FileContent = str_replace("【tableName】",$this->tableName, $FileContent);
		//写入文件
		return $FileContent;
	}

	/*创建Validate文件*/
	public function getValidateFile(){
		$validate = [
			"require" => "必须",
			"max" => "最多不能超过【length】个字符",
			"number" => "必须是数字",
		];

		$rules = "";
		$messages = "";
		$scene[] = "";
		foreach ($this->fields as $key => $field) {
			if(!$field['datatype']){
				continue;
			}
			$fieldname = empty($field['name']) ?$field['alias']: $field['name'];
			$rule = "";
			if($field['notnull']){
				$rule = "require|";
				$messages .= empty($messages) ? "\t\t" : "\n\t\t";
				$messages .= "'{$field['alias']}.require' => '{$fieldname}{$validate['require']}',";
			}
			$messages .= empty($messages) ? "\t\t" : "\n\t\t";
			if(strpos($field['datatype'], 'int')!==false){
				$rule .= 'number';
				$messages .= "'{$field['alias']}.number' => '{$fieldname}{$validate['number']}',";
			}else{
				$rule .= 'max:'.$field['datalength'];
				$messages .= "'{$field['alias']}.max' => '{$fieldname}".str_replace("【length】",$field['datalength'], $validate['max'])."',";
			}
			
			$rules .= empty($rules) ? "\t\t" : "\n\t\t";
			$rules .= "'{$field['alias']}' => '{$rule}',";

			$scene[] = "'{$field['alias']}'";
		}
		$FileContent = $this->readFile("Validate.php");
		$FileContent = str_replace("【rule】",$rules, $FileContent);
		$FileContent = str_replace("【message】",$messages, $FileContent);
		$FileContent = str_replace("【scene】", trim(implode(",",$scene),","), $FileContent);
		
		//替换
		$FileContent = str_replace("【ControllerName】",$this->ControllerName, $FileContent);
		//写入文件
		return $FileContent;
	}



	public function getIndexFile(){
		$FileContent = $this->readFile("index.html");
		$th_str = "";
		$td_str = "";

		foreach ($this->fields as $key => $field) {
			if($field['is_list'] == 1){
				$name = $field['name'];
				$alias = $field['alias'];

				$th_str .= $th_str == "" ? "\t\t\t\t\t\t<th>{$name}</th>" : "\n\t\t\t\t\t\t<th>{$name}</th>";
				$td_str .= $td_str == "" ? "\t\t\t\t\t\t\t" : "\n\t\t\t\t\t\t\t";
				if($field['type'] == 'input'||$field['type'] == 'password'){
					$td_str .= "<td class='layui-elip'>{\$vo.".$alias."}</td>";
				}else if($field['type'] == 'image'){
					$td_str .= "<td class='layui-elip'><img class='td_image' src=\"{\$vo.{$alias}}\" alt=\"{\$vo.{$alias}}\"  /></td>";
				}else if($field['type'] == 'select'){
					$array_str = "[";
					foreach ($field['default'] as $key => $vo) {
						$array_str .= "'{$vo['value']}'=>'{$vo['name']}',";
					}
					$array_str .= "]";
					$td_str .= "<td class='layui-elip'><?php \$array = {$array_str}; echo isset(\$array[\$vo['type']]) ? \$array[\$vo['type']]:'未知'; ?></td>";
				}
				
			}
		}

		$FileContent = str_replace("【th_str】",$th_str, $FileContent);
		$FileContent = str_replace("【td_str】",$td_str, $FileContent);
		$FileContent = str_replace("【title】",$this->title, $FileContent);

		return $FileContent;
	}

	public function getAddFile(){
		$FileContent = $this->readFile("add.html");
		$html_item = '';
		// dump( $this->fields );die();
		foreach ($this->fields as $key => $field) {
			if($field['is_edit'] == 0){
				continue;
			}

			$html_item .= $this->add_item($field);
		}
		$FileContent = str_replace("【html_item】",$html_item, $FileContent);
		$FileContent = str_replace("【title】",$this->title, $FileContent);

		return $FileContent;
	}

	public function getEditFile(){
		$FileContent = $this->readFile("edit.html");
		$FileContent = str_replace("【title】",$this->title, $FileContent);
		$html_item = '';
		foreach ($this->fields as $key => $field) {
			if($field['is_edit'] == 0){
				continue;
			}

			$html_item .= $this->add_item($field,"edit");
		}
		
		$FileContent = str_replace("【html_item】",$html_item, $FileContent);
		return $FileContent;
	}

	/*创建增加编辑的表单*/
	public function add_item($field,$action = 'add'){

		if($action == 'edit'){
			$edit_value = "value=\"{\$model['【字段标识】']}\"";
		}else{
			$edit_value = "";
		}

		switch ($field['type']) {
			#文本框
			case 'input':
			$html = "
			<div class=\"layui-form-item\">
				<label class=\"layui-form-label\">【字段名字】</label>
				<div class=\"layui-input-inline\">
					<input type=\"text\" name=\"【字段标识】\" placeholder=\"请输入【字段名字】\" class=\"layui-input\" {$edit_value}>
				</div>
			</div>
			";
			$html = str_replace( '【字段名字】', $field['name'] , $html);	
			$html = str_replace( '【字段标识】', $field['alias'] , $html);	
			break;
			#文本框
			case 'password':
			$html = "
			<div class=\"layui-form-item\">
				<label class=\"layui-form-label\">【字段名字】</label>
				<div class=\"layui-input-inline\">
					<input type=\"password\" name=\"【字段标识】\" placeholder=\"请输入【字段名字】\" class=\"layui-input\" {$edit_value}>
				</div>
			</div>
			";
			$html = str_replace( '【字段名字】', $field['name'] , $html);	
			$html = str_replace( '【字段标识】', $field['alias'] , $html);	
			break;

			#select
			case 'select':
			$option = "";

			foreach ($field['default'] as $key => $vo) {
			

				if($action == 'edit'){
					$option .= "<option value='{$vo['value']}' {if condition=\"\$model['【字段标识】'] == $key\"}selected{/if} >{$vo['name']}</option>";
				}else{
					$option .= "<option value='{$vo['value']}'>{$vo['name']}</option>";
				}
				
			}

			$html = "
            <div class=\"layui-form-item\">
            	<label class=\"layui-form-label\">【字段名字】</label>
                <div class=\"layui-input-inline\">
                    <select name=\"【字段标识】\">
                        {$option}
                    </select>
                </div>
            </div>
			";
			$html = str_replace( '【字段名字】', $field['name'] , $html);	
			$html = str_replace( '【字段标识】', $field['alias'] , $html);	
			break;

			#图片上传
			case 'image':
			$html = '
			<div class="layui-form-item">
				<label class="layui-form-label">【字段名字】</label>
				<div class="layui-input-inline image_upload_box">
					<input id="【字段标识】" type="text" name="【字段标识】" placeholder="请输入【字段名字】" class="layui-input preview_img" '.$edit_value.'>
				</div>
				<div class="layui-input-inline">
					<input type="file" name="【字段标识】IMA" lay-type="IMA" class="layui-upload-【字段标识】 layui-upload-file">
					<a href="javascript:;" class="layui-btn layui-btn-primary" onclick="preview_img(this)">预览</a>
				</div>
				<script type="text/javascript">
					layui.use(["upload"],function(){
						layui.upload({
							url: "{:Url(\'upload/image\')}"
							,elem: ".layui-upload-【字段标识】"
							,success: function(res){
								$("#【字段标识】").val(res.data.src);
							}
						});  
					});
				</script>
			</div>
			';
			$html = str_replace( '【字段名字】', $field['name'] , $html);	
			$html = str_replace( '【字段标识】', $field['alias'] , $html);	
			break;
			#内容框
			case 'editor':
			$html = '
			<div class="layui-form-item">
				<label class="layui-form-label">【字段名字】</label>
				<div class="layui-input-block">
				    <link href="/static/umeditor/themes/default/css/umeditor.css" type="text/css" rel="stylesheet">
				    <script type="text/javascript" src="/static/umeditor/third-party/jquery.min.js"></script>
				    <script type="text/javascript" src="/static/umeditor/third-party/template.min.js"></script>
				    <script type="text/javascript" charset="utf-8" src="/static/umeditor/umeditor.config.js"></script>
				    <script type="text/javascript" charset="utf-8" src="/static/umeditor/umeditor.min.js"></script>
				    <script type="text/javascript" src="/static/umeditor/lang/zh-cn/zh-cn.js"></script>

					<script type="text/plain" id="myEditor" name="【字段标识】" style="width:860px;height:240px;">'.($action == 'edit'? "{\$model['【字段标识】']}":"").'</script>
					<script type="text/javascript">
					    var um = UM.getEditor("myEditor");
					</script>
				</div>
			</div>
			';
			$html = str_replace( '【字段名字】', $field['name'] , $html);	
			$html = str_replace( '【字段标识】', $field['alias'] , $html);	
			break;
		}
		return $html;
	}

	public function getTableInfo($tableName){
		$info = $this->getFields($tableName);
		return $info;
	}

	public function getFields($tableName){
		$sql = 'SHOW FULL COLUMNS FROM ' . $tableName;
		$result = Db::query( $sql );

        $info   = [];
        if ($result) {
            foreach ($result as $key => $val) {
                $val = array_change_key_case($val);
                $info[$val['field']] = [
                    'name'    => $val['field'],
                    'type'    => $val['type'],
                    'notnull' => (bool) ('NO' === $val['null']),
                    'default' => $val['default'],
                    'primary' => (strtolower($val['key']) == 'pri'),
                    'autoinc' => (strtolower($val['extra']) == 'auto_increment'),
                    'comment' => $val['comment'],
                ];
            }
        }
		return $info;
	}

	public function readFile($fileName){
		$FileContent = file_get_contents("../application/admin/At/{$fileName}");
		return $FileContent;
	}

	//检查目录是否存在
	public function checkPath($path){
        if (!file_exists($path)){
            mkdir($path,0777,true); 
        }
	}

}