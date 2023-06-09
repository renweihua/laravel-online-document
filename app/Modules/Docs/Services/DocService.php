<?php

namespace App\Modules\Docs\Services;

use App\Exceptions\Exception;
use App\Exceptions\HttpStatus\BadRequestException;
use App\Exceptions\HttpStatus\ForbiddenException;
use App\Models\Docs\Doc;
use App\Models\Docs\OperationLog;
use App\Models\Docs\Project;
use App\Models\Docs\ProjectMember;
use App\Services\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocService extends Service
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
        $docBuild = Doc::with('userInfo');
        $lists = $docBuild
            ->where('project_id', $project_id)
            // 后期：管理员都可访问~
            // 还会存在对外可访问的文档
            ->where('user_id', $login_user_id)
            ->where(function ($query) use ($search, $group_id){
                if (!empty($search)){
                    $query->where('doc_name', 'LIKE', trim($search) . '%');
                }
                if ($group_id > 0){
                    $query->where('group_id', '=', $group_id);
                }
            })
            ->orderByDESC('is_top')
            ->orderByDESC('doc_id')
            ->paginate($this->getLimit());

        return $this->getPaginateFormat($lists);
    }

    protected function getDocById($doc_id, $role_power = ProjectMember::ROLE_POWER_READ)
    {
        $doc = Doc::with(['project', 'userInfo'])->find($doc_id);
        if (empty($doc)){
            throw new BadRequestException('文档不存在或已删除！');
        }
        // 验证访问权限
        $throw_msg = '您无权限查看文档`' . $doc->doc_name . '`！';
        switch ($role_power){
            case ProjectMember::ROLE_POWER_WRITE:
                $throw_msg = '您无权限编辑文档`' . $doc->doc_name . '`！';
                break;
            case ProjectMember::ROLE_POWER_DELETE_PROJECT_CHILDS:
                $throw_msg = '您无权限删除文档`' . $doc->doc_name . '`！';
                break;
        }
        Project::checkRolePowerThrow($doc->project, $role_power, $throw_msg);

        return $doc;
    }

    public function detail($doc_id)
    {
        return $this->getDocById($doc_id);
    }

    public function createOrUpdate($request)
    {
        $create = true;
        $doc_id = $request->input('doc_id', 0);
        if (!$doc_id){
            $project_id = $request->input('project_id');
            $project = Project::getDetailById($project_id);
            if (!$project){
                throw new BadRequestException('项目不存在或已删除！');
            }
            // 验证新增编辑权限
            Project::checkRolePowerThrow($project, ProjectMember::ROLE_POWER_WRITE);

            $detail = new Doc();
            $detail->user_id = getLoginUserId();
            $detail->project_id = $project->project_id;
        }else{
            $create = false;
            $detail = $this->getDocById($doc_id, ProjectMember::ROLE_POWER_WRITE);
        }

        DB::beginTransaction();
        try {
            $detail->doc_name = $request->input('doc_name');
            $detail->group_id = $request->input('group_id', 0);
            if ($request->has('content_html')){
                $detail->content_html = $request->input('content_html', '');
            }
            if ($request->has('content_markdown')){
                $detail->content_markdown = $request->input('content_markdown', '');
            }
            if ($request->has('sort')){
                $detail->sort = $request->input('sort');
            }
            $detail->save();

            DB::commit();
        }catch (Exception $e){
            DB::rollBack();
            throw new BadRequestException('文档' . ($create ? '创建' : '更新') . '失败，请重试！');
        }

        // 记录操作日志
        OperationLog::createLog(OperationLog::LOG_TYPE_DOC, $create ? OperationLog::ACTION['CREATE'] : OperationLog::ACTION['UPDATE'], $detail);

        return $detail;
    }

    public function setTop($doc_id, $is_top)
    {
        $detail = $this->getDocById($doc_id, ProjectMember::ROLE_POWER_WRITE);

        $detail->is_top = $is_top;
        $detail->save();

        return $detail;
    }

    public function delete($doc_id)
    {
        // 验证登录会员的权限
        $detail = $this->getDocById($doc_id, ProjectMember::ROLE_POWER_DELETE_PROJECT_CHILDS);

        $detail->delete();

        // 记录操作日志
        OperationLog::createLog(OperationLog::LOG_TYPE_DOC, OperationLog::ACTION['DELETE'], $detail);

        return $detail;
    }
}
