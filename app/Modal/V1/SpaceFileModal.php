<?php

namespace App\Modal\V1;
use Illuminate\Database\Eloquent\Model;

class SpaceFileModal extends Model{
    //数据库表名
    protected $table = 'spacefile';

    protected $primaryKey = 'vr_FileId';

    //数据库字段
    protected $fillable = ['vr_SpaceId', 'vr_ParentId', 'vr_UserId', 'vr_IsForder', 'vr_FileName', 'vr_ShowName', 'vr_FileType', 'vr_FileSize', 'vr_CreateTime', 'vr_UpdateTime'];

    //去除update_at等字段
    public $timestamps = false;
    
}