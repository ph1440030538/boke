<?php
namespace app\common\validate;
use think\Validate;

class MRole extends Validate
{
	protected $rule = [
		'name' => 'require|max:20',
		'pid' => 'number',
		'sort' => 'number',
		'remark' => 'max:255',
		'create_time' => 'number',
		'update_time' => 'number',
		'status' => 'number',
	];

	protected $message  = [
		'name.require' => '角色名称必须',
		'name.max' => '角色名称最多不能超过25个字符',
		'pid.number' => '父角色ID必须是数字',
		'sort.number' => '排序字段必须是数字',
		'remark.max' => '备注最多不能超过25个字符',
		'create_time.number' => '创建时间必须是数字',
		'update_time.number' => '更新时间必须是数字',
		'status.number' => '状态必须是数字',
	];

    protected $scene = [
        'add'   =>  ['name','pid','sort','remark','create_time','update_time','status'],
        'edit'  =>  ['name','pid','sort','remark','create_time','update_time','status'],
    ];    
}