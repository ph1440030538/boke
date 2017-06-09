<?php
namespace app\common\validate;
use think\Validate;

class MTest extends Validate
{
	protected $rule = [
		'name' => 'require|max:128',
		'keyword' => 'require|max:128',
		'image' => 'require|max:255',
		'type' => 'require|number',
		'create_time' => 'number',
		'update_time' => 'number',
		'status' => 'number',
		'banner_image' => 'max:255',
	];

	protected $message  = [
		'name.require' => '名字必须',
		'name.max' => '名字最多不能超过128个字符',
		'keyword.require' => '关键字必须',
		'keyword.max' => '关键字最多不能超过128个字符',
		'image.require' => '图片必须',
		'image.max' => '图片最多不能超过255个字符',
		'type.require' => '类型必须',
		'type.number' => '类型必须是数字',
		'create_time.number' => '创建时间必须是数字',
		'update_time.number' => '更新时间必须是数字',
		'status.number' => '状态必须是数字',
		'banner_image.max' => 'banner图片最多不能超过255个字符',
	];

    protected $scene = [
        'add'   =>  ['name','keyword','image','type','create_time','update_time','status','banner_image'],
        'edit'  =>  ['name','keyword','image','type','create_time','update_time','status','banner_image'],
    ];    
}