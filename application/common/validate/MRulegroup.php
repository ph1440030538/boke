<?php
namespace app\common\validate;
use think\Validate;

class MRulegroup extends Validate
{
	protected $rule = [
		'group_name' => 'require',
	];

	protected $message  = [
		'group_name.require' => '权限名字必须',
	];

    protected $scene = [
        'add'   =>  ['group_name'],
        'edit'  =>  ['group_name'],
    ];  
}