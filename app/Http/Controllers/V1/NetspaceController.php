<?php

namespace App\Http\Controllers\V1;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Modal\V1\SpaceFileModal;
use App\Modal\V1\SpaceTypeModal;

use Storage;
use App\Transformers\V1\SpaceFileTransformer;
use Dingo\Api\Routing\Helpers;

class NetspaceController extends BaseController{

    use Helpers;

    private $userId = '';
    private $allowExt = array('txt', 'jpeg', 'jpg', 'png');

    /**
     * @OA\Info(title="网盘管理", version="0.1")
     */

    /**
     * @OA\Get(
     *     path="/netspace/test",
     *     summary="检查邮件是否已被注册",
     *     tags={"网盘"},
     *     @OA\Response(response="200", description="success"),
     *     @OA\Parameter(
     *         parameter="email",
     *         name="email",
     *         description="邮箱地址",
     *         @OA\Schema(
     *             type="string"
     *         ),
     *         in="query",
     *         required=true
     *     ),
     * )
     */
    public function test(){
        $result = DB::select('select * from zones');
        return array('result'=>$result);
    }

    public function AddForder(Request $request){
        $this->userId = $this->checkAuthUser($request);
        $v = $this->validate($request, [
            'name'=>'required|string',
            'parentid'=>'string',
            'spaceid'=>'string'
        ]);

        //父级文件夹需要存在，并且是文件夹类型，并且是我自己创建的
        $parentid = isset($v['parentid']) ? $v['parentid'] : -1;
        if($parentid != -1){
            $this->_checkIsMyForder($parentid);
        }

        //检查网盘类型是否存在
        $spaceId = isset($v['spaceid']) ? $v['spaceid'] : 1;
        if($spaceId != 1){
            $this->_checkSpaceTypeIsExist($spaceId);
        }

        //文件夹已存在，动态新建名称
        $forder = SpaceFileModal::where([
            ['vr_ShowName', '=', $v['name']],
            ['vr_ParentId', '=', $parentid]
        ])->first();
        if($forder){
            //重命名
            $v['name'] = $this->_rename($v['name'], $parentid);
        }
        //插入数据
        $forderinfor = array(
            'vr_ParentId'=>$parentid,
            'vr_SpaceId'=>$spaceId,
            'vr_UserId'=>$this->userId,
            'vr_IsForder'=>1,
            'vr_FileName'=>'--',
            'vr_ShowName'=>$v['name'],
            'vr_FileType'=>'--',
            'vr_FileSize'=>'--',
            'vr_CreateTime'=>date('Y-m-d H:i:s'),
            'vr_UpdateTime'=>date('Y-m-d H:i:s')
        );

        $ret = SpaceFileModal::create($forderinfor);

        return $this->response->item($ret, new SpaceFileTransformer);
    }

    public function RenameForder(Request $request, $fileid){
        $this->userId = $this->checkAuthUser($request);
        $v = $this->validate($request, [
            'name'=>'required|string'
        ]);

        //判断文件夹是否存在
        $forder = $this->_checkFile($fileid);
        $parentid = $forder->vr_ParentId;
        
        //判断文件夹名字是否存在
        $chkforder = SpaceFileModal::where([
            ['vr_ShowName', '=', $v['name']],
            ['vr_ParentId', '=', $parentid]
        ])->first();
        if($chkforder && $chkforder->vr_FileId != $forder->vr_FileId){
            //重命名
            $v['name'] = $this->_rename($v['name'], $parentid);
        }

        $forder->vr_ShowName = $v['name'];
        $forder->save();

        return $this->response->item($forder, new SpaceFileTransformer);
        
    }

    public function DeleteForderFiles(Request $request, $fileid){
        $this->userId = $this->checkAuthUser($request);
        //判断文件夹是否存在,是否有权限
        $forder = $this->_checkFile($fileid);
        //删除的是文件夹，将文件夹下面的文件都删除
        $this->_deleteForderfiles($forder);

        return $this->response->noContent();
    }


    public function AddFile(Request $request){
        $this->userId = $this->checkAuthUser($request);
        $v = $this->validate($request, [
            'parentid'=>'string',
            'spaceid'=>'string'
        ]);
        //父级文件夹需要存在，并且是文件夹类型，并且是我自己创建的
        $parentid = isset($v['parentid']) ? $v['parentid'] : -1;
        if($parentid != -1){
            $this->_checkIsMyForder($parentid);
        }

        //检查网盘类型是否存在
        $spaceId = isset($v['spaceid']) ? $v['spaceid'] : 1;
        if($spaceId != 1){
            $this->_checkSpaceTypeIsExist($spaceId);
        }

        $ret = $this->_checkUploadFile($request);
        $path = $ret[0];
        $size = $ret[1];
        $filename = $ret[2];
        $ext = $ret[3];
        
        $infor = array(
            'vr_ParentId'=>$parentid,
            'vr_SpaceId'=>$spaceId,
            'vr_UserId'=>$this->userId,
            'vr_IsForder'=>0,
            'vr_FileName'=>$path,
            'vr_ShowName'=>$filename,
            'vr_FileType'=>$ext,
            'vr_FileSize'=>$size,
            'vr_CreateTime'=>date('Y-m-d H:i:s'),
            'vr_UpdateTime'=>date('Y-m-d H:i:s')
        );
        
        $ret = SpaceFileModal::create($infor);

        return $this->response->item($ret, new SpaceFileTransformer);
    }

    public function GetFiles(Request $request){
        $this->userId = $this->checkAuthUser($request);
        //Integer
        $v = $this->validate($request, [
            'parentid'=>'string',
            'spaceid'=>'string'
        ]);
        
        $spaceId = isset($v['spaceid']) ? $v['spaceid'] : 1;
        $spacetype = $this->_checkSpaceTypeIsExist($spaceId);

        if($spacetype->vr_SpaceType == 'personal'){
            //只能看自己的
            $parentid = isset($v['parentid']) ? $v['parentid'] : -1;
            if($parentid != -1){
                $this->_checkIsMyForder($parentid);
            }
            $files = SpaceFileModal::where([
                ['vr_ParentId', '=', $parentid],
                ['vr_UserId', '=', $this->userId],
                ['vr_SpaceId', '=', $spaceId]
            ])->paginate(15);

            return $this->response->array($files);

        }else if($spacetype->vr_SpaceType == 'public'){
            //谁的都能看
            $parentid = isset($v['parentid']) ? $v['parentid'] : -1;

            $files = SpaceFileModal::where([
                ['vr_ParentId', '=', $parentid],
                ['vr_SpaceId', '=', $spaceId]
            ])->paginate(15);

            return $this->response->array($files);
        }
    }

    public function DownloadFile(Request $request, $fileid){
        $this->userId = $this->checkAuthUser($request);
        //return Storage::download('file.jpg');
        $ff = $this->_checkFile($fileid);
        $path = $ff->vr_FileName;
        return Storage::disk('uploads')->download($path, $ff->vr_ShowName);
    }

    public function Downloadpicture(Request $request, $fileid){
        $ff = SpaceFileModal::find($fileid);
        if(!$ff){
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('文件/文件夹不存在');
        }
        $path = $ff->vr_FileName;
        return Storage::disk('uploads')->download($path, $ff->vr_ShowName);
    }

    public function GetFileById(Request $request, $fileid){
        $this->userId = $this->checkAuthUser($request);
        $ff = $this->_checkFile($fileid);

        return $ff;
    }


    //////////////////////////////////////////////////////////////////////
    private function _checkUploadFile($request){
        if(!$request->hasFile('file')){
            throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException('请选择一个文件');
        }
        $extension = $request->file->extension();

        if(!in_array($extension, $this->allowExt)){
            throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException('不支持的文件类型');
        }

        $path = $request->file->store('person/user_'.$this->userId, 'uploads');
        if (!$request->file('file')->isValid()) {
            throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException('不支持的文件类型');
        }
        $filesize = $request->file->getClientSize();
        $filename = $request->file->getClientOriginalName();

        return [$path, $filesize, $filename, $extension];
    }
    public function _trans_filesize($bytes, $decimals = 2){
        $size = ['B', 'kB', 'MB', 'GB', 'TB', 'PB'];
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) .@$size[$factor];
    }
    private function _deleteForderfiles($forder){
        if($forder->vr_IsForder){
            $files = SpaceFileModal::where('vr_ParentId', $forder->vr_FileId)->get();
            foreach($files as $ff){
                $this->_deleteForderfiles($ff);
            }
            $forder->delete();
        }else{
            $forder->delete();
            Storage::disk('uploads')->delete($forder->vr_FileName);
        }
    }
    private function _rename($name, $parentid){
        $oldvname = $name;
        $i = 1;
        $name = $oldvname.'（'.$i.'）';
        while(SpaceFileModal::where([['vr_ShowName', '=', $name], ['vr_ParentId', '=', $parentid]])->first()){
            $i++;
            $name = $oldvname.'（'.$i.'）';
        }
        return $name;
    }
    private function _checkIsMyFile($forderfile){
        if($forderfile && $forderfile->vr_UserId != $this->userId){
            throw new \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException('您没有权限操作');
        }
    }
    private function _checkFile($fileid){
        $ff = SpaceFileModal::find($fileid);
        if(!$ff){
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('文件/文件夹不存在');
        }
        $this->_checkIsMyFile($ff);
        return $ff;
    }
    private function _checkIsMyForder($fileid){
        $ff = $this->_checkFile($fileid);
        if(!$ff->vr_IsForder){
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('文件夹不存在');
        }
        return $ff;
    }
    private function _checkSpaceTypeIsExist($spaceId){
        $spaceType = SpaceTypeModal::find($spaceId);
        if(!$spaceType){
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('网盘不存在');
        }
        return $spaceType;
    }
    private function checkAuthUser($request){
        $userid = $request->input('userid');
        
        if(!$userid){
            throw new \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException('no auth');
        }
        return $userid;
    }

}