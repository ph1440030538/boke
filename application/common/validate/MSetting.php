<?php
namespace app\common\validate;
use think\Validate;

class MSetting extends Validate
{
	protected $rule = [
		'setting_key' => 'require|max:128',
		'setting_value' => 'require',
		'setting_type' => 'require',
	];

	protected $message  = [
		'setting_key.require' => '标识一定要设置',
		'setting_key.max' => '标识最多不能超过128个字符',
		'setting_value.require' => '设置值必须',
		'setting_type.require' => '类型一定要设置',
	];

    protected $scene = [
        'add'   =>  ['setting_key','setting_value','setting_type'],
        'edit'  =>  ['setting_key','setting_value','setting_type'],
    ];    
}