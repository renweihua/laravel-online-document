<?php

namespace App\Modules\Docs\Services;

use App\Exceptions\Exception;
use App\Exceptions\HttpStatus\BadRequestException;
use App\Exceptions\HttpStatus\ForbiddenException;
use App\Models\Docs\Doc;
use App\Models\Docs\Project;
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
        $group_id = $request->input('group_id', 0);
        $search = $request->input('search', '');
        $docBuild = Doc::getInstance();
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
            ->orderByDESC('id')
            ->paginate($this->getLimit());

        return $this->getPaginateFormat($lists);
    }

    protected function getDocById($id, $with = [], $check_auth = true)
    {
        $doc = Doc::with(array_merge(['project'], $with))->find($id);
        if (empty($doc)){
            throw new BadRequestException('文档不存在或已删除！');
        }
        if ($check_auth && $doc->user_id != getLoginUserId()){
            throw new ForbiddenException('您无权限查看文档`' . $doc->project_name . '`！');
        }
        return $doc;
    }

    public function detail($id)
    {
        return $this->getDocById($id);
    }

    public function createOrUpdate($request)
    {
        $id = $request->input('id', 0);
        if (!$id){
            $doc = new Doc();
            $doc->user_id = getLoginUserId();
            $doc->project_id = $request->input('project_id');
        }else{
            $doc = $this->getDocById($id);
        }

        DB::beginTransaction();
        try {
            $doc->doc_name = $request->input('doc_name');
            $doc->group_id = $request->input('group_id', 0);
            if ($request->has('content_html')){
                $doc->content_html = $request->input('content_html', '');
            }
            if ($request->has('content_markdown')){
                $doc->content_markdown = $request->input('content_markdown', '');
            }
            if ($request->has('sort')){
                $doc->sort = $request->input('sort');
            }
            $doc->save();

            DB::commit();

            return $doc;
        }catch (Exception $e){
            DB::rollBack();
            throw new BadRequestException('文档更新失败，请重试！');
        }
    }
}
