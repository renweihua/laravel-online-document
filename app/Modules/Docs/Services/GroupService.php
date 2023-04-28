<?php

namespace App\Modules\Docs\Services;

use App\Exceptions\Exception;
use App\Exceptions\HttpStatus\BadRequestException;
use App\Exceptions\HttpStatus\ForbiddenException;
use App\Models\Docs\Group;
use App\Models\Docs\OperationLog;
use App\Services\Service;
use Illuminate\Support\Facades\DB;

class GroupService extends Service
{
    public function index()
    {
        $login_user_id = getLoginUserId();
        $request = request();
        $project_id = $request->input('project_id');
        if (empty($project_id)) {
            return $this->getPaginateFormat([]);
        }
        $parent_id = $request->input('parent_id', 0);
        $group_type = $request->input('group_type', Group::GROUP_TYPE_API);
        $search = $request->input('search', '');
        $docBuild = Group::getInstance();
        $lists = $docBuild
            ->where('project_id', $project_id)
            // 分组类型
            ->where('group_type', $group_type)
            // 后期：管理员都可访问~
            // 还会存在对外可访问的文档
            ->where('user_id', $login_user_id)
            ->where(function ($query) use ($search, $parent_id){
                if (!empty($search)){
                    $query->where('group_name', 'LIKE', trim($search) . '%');
                }
                if ($parent_id > 0){
                    $query->where('parent_id', '=', $parent_id);
                }
            })
            ->orderByDESC('group_id')
            ->get();

        // Tree结构
        $lists = list_to_tree($lists->toArray(), 'group_id');

        return $lists;
    }

    protected function getDocById($doc_id, $with = [], $check_auth = true)
    {
        $doc = Group::with(array_merge(['project', 'userInfo'], $with))->find($doc_id);
        if (empty($doc)){
            throw new BadRequestException('文档不存在或已删除！');
        }
        if ($check_auth && $doc->user_id != getLoginUserId()){
            throw new ForbiddenException('您无权限查看文档`' . $doc->project_name . '`！');
        }
        return $doc;
    }

    public function detail($group_id)
    {
        return $this->getDocById($group_id);
    }

    public function createOrUpdate($request)
    {
        $create = true;
        $group_id = $request->input('group_id', 0);
        if (!$group_id){
            $detail = new Group();
            $detail->user_id = getLoginUserId();
            $detail->project_id = $request->input('project_id');
            $detail->group_type = $request->input('group_type');
        }else{
            $create = false;
            $detail = $this->getDocById($group_id);
        }

        DB::beginTransaction();
        try {
            $detail->group_name = $request->input('group_name');
            if ($request->has('parent_id')){
                $detail->parent_id = $request->input('parent_id');
            }
            if ($request->has('sort')){
                $detail->sort = $request->input('sort');
            }
            if ($request->has('default_expand')){
                $detail->default_expand = $request->input('default_expand');
            }
            $detail->save();

            DB::commit();
        }catch (Exception $e){
            DB::rollBack();
            throw new BadRequestException('分组' . ($create ? '创建' : '更新') . '失败，请重试！');
        }

        // 记录操作日志
        OperationLog::createLog(OperationLog::LOG_TYPE_GROUP, $create ? OperationLog::ACTION['CREATE'] : OperationLog::ACTION['UPDATE'], $detail);

        return $detail;
    }
}
