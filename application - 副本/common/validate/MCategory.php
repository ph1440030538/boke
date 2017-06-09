<?php
namespace app\common\validate;
use think\Validate;

class MCategory extends Validate
{
    protected $rule = [
        'name'  =>  'require|max:25',
        'parent_id' =>  'require',
    ];

    protected $message = [
        'name.require'  =>  '用户名必须',
    ];

    protected $scene = [
        'add'   =>  ['name'],
        'edit'  =>  ['name'],
    ];    
}