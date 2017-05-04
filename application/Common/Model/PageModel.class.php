<?php
namespace Common\Model;
use Common\Model\CommonModel;
class PageModel extends CommonModel{

	protected $_validate = array(
		array('title', 'require', '页面标题为空！', 1, 'regex', CommonModel:: MODEL_BOTH ),
		array('page_type', 'require', '页面类型为空！', 1, 'regex', CommonModel:: MODEL_BOTH ),
	);

	protected $_auto = array ( 
	    array('create_time','time',1,'function'),
	    array('update_time','time',3,'function'),
	);

	protected function _before_write(&$data) {
		parent::_before_write($data);
		$data['term_ids'] = json_encode($data['term_ids'] ?: []);

	}

	public function getPages(){
		$template_path = "themes\\simplebootx\\pages\\";
		$files=sp_scan_dir($template_path."*");
		$tpl_files=array();
		foreach ($files as $f){
			if($f!="." || $f!=".."){

				if(is_file($template_path.$f)){
					$suffix=C("TMPL_TEMPLATE_SUFFIX");
					$result=preg_match("/$suffix$/", $f);
					if($result){
						$tpl=str_replace($suffix, "", $f);
						$tpl_files[$tpl]=$tpl;
					}else if(preg_match("/\.php$/", $f)){
					    $tpl=str_replace($suffix, "", $f);
					    $tpl_files[$tpl]=$tpl;
					}
				}
			}
		}
		return $tpl_files;
	}
}