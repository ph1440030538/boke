<?php

switch ($field) {
	case 'input':
$html ="
			<div class=\"layui-form-item\">
				<label class=\"layui-form-label\">标题</label>
				<div class=\"layui-input-inline\">
					<input type=\"text\" name=\"title\" placeholder=\"请输入标题\" class=\"layui-input\" lay-verify=\"required\">
				</div>
			</div>
";


		break;
	
	default:
		# code...
		break;
}
