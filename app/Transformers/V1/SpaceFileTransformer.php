<?php

namespace App\Transformers\V1;

use League\Fractal\TransformerAbstract;
use App\Modal\V1\SpaceFileModal;

class SpaceFileTransformer extends TransformerAbstract
{
    public function transform(SpaceFileModal $file) {
        return [
            'fileid' => $file['vr_FileId'],
            'parentid' => $file['vr_ParentId'],
            'spaceid' => $file['vr_SpaceId'],
            'userid' => $file['vr_UserId'],
            'isforder' => $file['vr_IsForder'],
            'filename' => $file['vr_FileName'],
            'showname' => $file['vr_ShowName'],
            'filetype' => $file['vr_FileType'],
            'filesize' => $file['vr_FileSize'],
            'ctime' => $file['vr_CreateTime'],
            'utime' => $file['vr_UpdateTime']
        ];
    }
}