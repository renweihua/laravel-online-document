<?php

namespace App\Modules\Docs\Services;

use App\Exceptions\Exception;
use App\Exceptions\HttpStatus\BadRequestException;
use App\Exceptions\HttpStatus\ForbiddenException;
use App\Models\Docs\Api;
use App\Models\Docs\Doc;
use App\Models\Docs\Project;
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

    protected function getDetailcById($api_id, $with = [], $check_auth = true)
    {
        $detail = Api::with(array_merge(['project', 'userInfo'], $with))->find($api_id);
        if (empty($detail)){
            throw new BadRequestException('API不存在或已删除！');
        }
        if ($check_auth && $detail->user_id != getLoginUserId()){
            throw new ForbiddenException('您无权限查看API`' . $detail->api_name . '`！');
        }
        return $detail;
    }

    public function detail($doc_id)
    {
        return $this->getDetailcById($doc_id);
    }

    public function createOrUpdate($request)
    {
        $api_id = $request->input('api_id', 0);
        if (!$api_id){
            $detail = new Api();
            $detail->user_id = getLoginUserId();
            $detail->project_id = $request->input('project_id');
        }else{
            $detail = $this->getDetailcById($api_id);
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
            $detail->object_name = $request->input('object_name');
            $detail->function_name = $request->input('function_name');
            $detail->develop_language = $request->input('develop_language');
            $detail->http_header = $request->input('http_header', []);
            $detail->http_params = $request->input('http_params', []);
            $detail->http_return_type = $request->input('http_return_type');
            $detail->response_params = $request->input('response_params', []);
            $detail->response_sample = $request->input('response_sample', []);
            $detail->save();

            DB::commit();

            return $detail;
        }catch (Exception $e){
            DB::rollBack();
            throw new BadRequestException('API更新失败，请重试！');
        }
    }
}
