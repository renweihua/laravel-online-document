<?php

namespace App\Modules\Docs\Services;

use App\Exceptions\Exception;
use App\Exceptions\HttpStatus\BadRequestException;
use App\Exceptions\HttpStatus\ForbiddenException;
use App\Models\Docs\Doc;
use App\Models\Docs\Group;
use App\Models\Docs\Project;
use App\Services\Service;
use Illuminate\Http\Request;
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
            ->orderByDESC('group_id')
            ->paginate($this->getLimit());

        return $this->getPaginateFormat($lists);
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
        $group_id = $request->input('group_id', 0);
        if (!$group_id){
            $detail = new Group();
            $detail->user_id = getLoginUserId();
            $detail->project_id = $request->input('project_id');
            $detail->group_type = $request->input('group_type');
        }else{
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
            $detail->save();

            DB::commit();

            return $detail;
        }catch (Exception $e){
            DB::rollBack();
            throw new BadRequestException('分组更新失败，请重试！');
        }
    }
}
