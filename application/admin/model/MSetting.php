<?php
namespace app\admin\model;

use think\Model;
use think\Db;
use think\Loader;
use extend\Tree;

class MSetting extends Model
{
	protected $table = 'boke_settion';
	protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    
}