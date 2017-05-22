<?php
namespace app\common\validate;
use think\Validate;

class MUser extends Validate
{
	protected $rule = [
		'avatar' => 'max:255',
		'username' => 'require|max:128',
		'password' => 'max:128',
		'create_time' => 'number',
		'update_time' => 'number',
		'status' => 'number',
	];

	protected $message  = [
		'avatar.max' => '头像最多不能超过25个字符',
		'username.require' => '用户名必须',
		'username.max' => '用户名最多不能超过25个字符',
		'password.max' => '密码最多不能超过25个字符',
		'create_time.number' => 'create_time必须是数字',
		'update_time.number' => 'update_time必须是数字',
		'status.number' => 'status必须是数字',
	];

    protected $scene = [
        'add'   =>  ['avatar','username','password','create_time','update_time','status'],
        'edit'  =>  ['avatar','username','password','create_time','update_time','status'],
    ];    
}