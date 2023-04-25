<?php

namespace App\Modules\Docs\Services;

use App\Exceptions\Exception;
use App\Exceptions\HttpStatus\BadRequestException;
use App\Exceptions\HttpStatus\ForbiddenException;
use App\Models\Docs\FieldMapping;
use App\Models\Docs\Project;
use App\Services\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FieldMappingService extends Service
{
    public function index()
    {
        $login_user_id = getLoginUserId();
        $request = request();
        $project_id = $request->input('project_id');
        if (empty($project_id)) {
            return $this->getPaginateFormat([]);
        }
        $field_type = $request->input('field_type', '');
        $search = $request->input('search', '');
        $build = FieldMapping::getInstance();
        $lists = $build
            ->where('project_id', $project_id)
            ->where(function ($query) use ($search, $field_type){
                if (!empty($search)){
                    $query->where('field_name', 'LIKE', trim($search) . '%');
                }
                if (!empty($field_type)){
                    $query->where('field_type', '=', $field_type);
                }
            })
            ->orderByDESC('id')
            ->paginate($this->getLimit());

        return $this->getPaginateFormat($lists);
    }

    protected function getProjectById($project_id, $with = [], $check_auth = true)
    {
        $detail = FieldMapping::with(array_merge(['userInfo'], $with))->find($project_id);
        if (empty($detail)){
            throw new BadRequestException('字段映射不存在或已删除！');
        }
        if ($check_auth && $detail->user_id != getLoginUserId()){
            throw new ForbiddenException('您无权限查看字段映射`' . $detail->field_name . '`！');
        }
        return $detail;
    }

    public function detail($id)
    {
        return $this->getProjectById($id);
    }

    public function createOrUpdate(Request $request)
    {
        $id = $request->input('id', 0);
        if (!$id){
            $detail = new FieldMapping();
            $detail->user_id = getLoginUserId();
            $detail->project_id = $request->input('project_id');
        }else{
            $detail = $this->getProjectById($id);
        }

        DB::beginTransaction();
        try {
            $detail->field_name = $request->input('field_name');
            $detail->field_type = $request->input('field_type');
            if ($request->has('field_description')){
                $detail->field_description = $request->input('field_description', '');
            }
            $detail->save();

            DB::commit();

            return $detail;
        }catch (Exception $e){
            DB::rollBack();
            throw new BadRequestException('字段映射更新失败，请重试！');
        }
    }
}
