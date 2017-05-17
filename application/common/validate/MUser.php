<?php
namespace app\common\validate;
use think\Validate;

class MUser extends Validate
{
    protected $rule = [
        'avatar'  =>  'max:255',
        'username'  =>  'max:255',
        'password'  =>  'max:128',
        'create_time'  =>  'number',
        'update_time'  =>  'number',
        'status'  =>  'number'
    ];

    protected $message = [
        'avatar.max'  =>  'avatar最多不能超过25个字符',
        'username.max'  =>  'username最多不能超过25个字符',
        'password.max'  =>  'password最多不能超过25个字符',
        'create_time.number'  =>  'create_time必须为数字',
        'update_time.number'  =>  'update_time必须为数字',
        'status.number'  =>  'status必须为数字',
    ];

    protected $scene = [
        'add'   =>  ['avatar','username','password','create_time','update_time','status'],
        'edit'  =>  ['avatar','username','password','create_time','update_time','status'],
    ];  
}