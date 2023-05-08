<?php

namespace App\Modules\Docs\Services;

use App\Exceptions\Exception;
use App\Exceptions\HttpStatus\BadRequestException;
use App\Exceptions\HttpStatus\ForbiddenException;
use App\Models\Docs\FieldMapping;
use App\Models\Docs\OperationLog;
use App\Models\Docs\Project;
use App\Models\Docs\ProjectMember;
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
        // 验证访问权限
        $project = Project::getDetailById($project_id);
        Project::checkRolePowerThrow($project);

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

    protected function getDetailById($id, $role_power = ProjectMember::ROLE_POWER_READ)
    {
        $detail = FieldMapping::with(['userInfo'])->find($id);
        if (empty($detail)){
            throw new BadRequestException('字段映射不存在或已删除！');
        }
        // 验证访问权限
        $throw_msg = '您无权限查看字段映射`' . $detail->field_name . '`！';
        switch ($role_power){
            case ProjectMember::ROLE_POWER_WRITE:
                $throw_msg = '您无权限编辑字段映射`' . $detail->field_name . '`！';
                break;
            case ProjectMember::ROLE_POWER_DELETE_PROJECT_CHILDS:
                $throw_msg = '您无权限删除字段映射`' . $detail->field_name . '`！';
                break;
        }
        Project::checkRolePowerThrow($detail->project, $role_power, $throw_msg);

        return $detail;
    }

    public function detail($id)
    {
        return $this->getDetailById($id);
    }

    public function createOrUpdate(Request $request)
    {
        $create = true;
        $id = $request->input('id', 0);
        if (!$id){
            $project_id = $request->input('project_id');
            $project = Project::getDetailById($project_id);
            if (!$project){
                throw new BadRequestException('项目不存在或已删除！');
            }
            // 验证新增编辑权限
            Project::checkRolePowerThrow($project, ProjectMember::ROLE_POWER_WRITE);

            $detail = new FieldMapping();
            $detail->user_id = getLoginUserId();
            $detail->project_id = $project->project_id;
        }else{
            $create = false;
            $detail = $this->getDetailById($id, ProjectMember::ROLE_POWER_WRITE);
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
        }catch (Exception $e){
            DB::rollBack();
            throw new BadRequestException('字段映射' . ($create ? '创建' : '更新') . '失败，请重试！');
        }

        // 记录操作日志
        OperationLog::createLog(OperationLog::LOG_TYPE_FIELD_MAPPING, $create ? OperationLog::ACTION['CREATE'] : OperationLog::ACTION['UPDATE'], $detail);

        return $detail;
    }

    public function delete($id)
    {
        $detail = $this->getDetailById($id, ProjectMember::ROLE_POWER_DELETE_PROJECT_CHILDS);

        $detail->delete();

        // 记录操作日志
        OperationLog::createLog(OperationLog::LOG_TYPE_FIELD_MAPPING, OperationLog::ACTION['DELETE'], $detail);

        return $detail;
    }
}
