<?php

namespace App\Modules\Docs\Services;

use App\Constants\HttpStatus;
use App\Exceptions\Exception;
use App\Exceptions\HttpStatus\BadRequestException;
use App\Exceptions\HttpStatus\ForbiddenException;
use App\Models\Docs\Api;
use App\Models\Docs\OperationLog;
use App\Models\Docs\Project;
use App\Models\Docs\ProjectMember;
use App\Services\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiService extends Service
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

        $group_id = $request->input('group_id', 0);
        $search = $request->input('search', '');
        $build = Api::with('userInfo');
        $lists = $build
            ->where('project_id', $project_id)
            // 后期：管理员都可访问~
            // 还会存在对外可访问的文档
            ->where('user_id', $login_user_id)
            ->where(function ($query) use ($search, $group_id){
                if (!empty($search)){
                    $query->where('api_name', 'LIKE', trim($search) . '%');
                }
                if ($group_id > 0){
                    $query->where('group_id', '=', $group_id);
                }
            })
            ->orderByDESC('api_id')
            ->paginate($this->getLimit());

        return $this->getPaginateFormat($lists);
    }

    protected function getDetailcById($api_id, $role_power = ProjectMember::ROLE_POWER_READ)
    {
        $detail = Api::with(['project', 'group', 'userInfo'])->find($api_id);
        if (empty($detail)){
            throw new BadRequestException('API不存在或已删除！');
        }
        // 验证访问权限
        $throw_msg = '您无权限查看API`' . $detail->api_name . '`！';
        switch ($role_power){
            case ProjectMember::ROLE_POWER_WRITE:
                $throw_msg = '您无权限编辑API`' . $detail->api_name . '`！';
                break;
            case ProjectMember::ROLE_POWER_DELETE_PROJECT_CHILDS:
                $throw_msg = '您无权限删除API`' . $detail->api_name . '`！';
                break;
        }
        Project::checkRolePowerThrow($detail->project, $role_power, $throw_msg);

        return $detail;
    }

    public function detail($doc_id)
    {
        return $this->getDetailcById($doc_id);
    }

    public function createOrUpdate($request)
    {
        $create = true;
        $api_id = $request->input('api_id', 0);
        if (!$api_id){
            $project_id = $request->input('project_id');
            $project = Project::getDetailById($project_id);
            if (!$project){
                throw new BadRequestException('项目不存在或已删除！');
            }
            // 验证新增编辑权限
            Project::checkRolePowerThrow($project, ProjectMember::ROLE_POWER_WRITE);

            $detail = new Api();
            $detail->user_id = getLoginUserId();
            $detail->project_id = $project->project_id;
        }else{
            $create = false;
            $detail = $this->getDetailcById($api_id, ProjectMember::ROLE_POWER_WRITE);
        }

        DB::beginTransaction();
        try {
            $detail->group_id = $request->input('group_id', 0);
            $detail->api_url = $request->input('api_url');
            $detail->api_name = $request->input('api_name');
            $detail->api_description = $request->input('api_description', '');
            // 请求协议
            if ($request->has('http_protocol')){
                $detail->http_protocol = $request->input('http_protocol', '');
            }
            // 请求方式
            if ($request->has('http_method')){
                $detail->http_method = $request->input('http_method', '');
            }
            $detail->develop_language = $request->input('develop_language');
            $detail->http_header = $request->input('http_header', []);
            $detail->http_params = $request->input('http_params', []);
            $detail->http_status = $request->input('http_status', HttpStatus::SUCCESS);
            $detail->response_params = $request->input('response_params', []);
            $detail->response_sample = $request->input('response_sample', []);
            $detail->save();

            DB::commit();
        }catch (Exception $e){
            DB::rollBack();
            throw new BadRequestException('API' . ($create ? '创建' : '更新') . '失败，请重试！');
        }

        // 记录操作日志
        OperationLog::createLog(OperationLog::LOG_TYPE_API, $create ? OperationLog::ACTION['CREATE'] : OperationLog::ACTION['UPDATE'], $detail);

        return $detail;
    }

    public function delete(Request $request)
    {
        $id = $request->input('api_id');
        // 验证登录会员的项目权限
        $detail = $this->getDetailcById($id, ProjectMember::ROLE_POWER_DELETE_PROJECT_CHILDS);

        $detail->delete();

        // 记录操作日志
        OperationLog::createLog(OperationLog::LOG_TYPE_API, OperationLog::ACTION['DELETE'], $detail);

        return $detail;
    }
}
