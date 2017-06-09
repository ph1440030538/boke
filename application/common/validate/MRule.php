<?php
namespace app\common\validate;
use think\Validate;

class MRule extends Validate
{
	protected $rule = [
		'rule_name' => 'require',
		'rule' => 'require',
	];

	protected $message  = [
		'rule_name.require' => '权限名字必须',
		'rule.require' => '权限规则必须',
	];

    protected $scene = [
        'add'   =>  ['rule_name','rule'],
        'edit'  =>  ['rule_name','rule'],
    ];    
}