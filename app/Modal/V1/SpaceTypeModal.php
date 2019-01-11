<?php

namespace App\Modal\V1;
use Illuminate\Database\Eloquent\Model;

class SpaceTypeModal extends Model{
    //数据库表名
    protected $table = 'spacetype';

    protected $primaryKey = 'vr_SpaceId';

    //数据库字段
    protected $fillable = ['vr_SpaceType', 'vr_SpaceName', 'vr_SpaceSize', 'vr_CreateTime', 'vr_UpdateTime'];

    //去除update_at等字段
    public $timestamps = false;
    
}