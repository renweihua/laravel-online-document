<?php

namespace App\Modules\Docs\Http\Requests;

use App\Http\Requests\BaseRequest;
use App\Models\Docs\Project;
use Illuminate\Validation\Rule;

class ProjectMemberSetLeaderRequest extends BaseRequest
{
    protected $projectModel;

    public function setInstance()
    {
        $this->projectModel = Project::getInstance();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'project_id' => [
                'required',
                Rule::exists($this->projectModel->getTable(), $this->projectModel->getKeyName()),
            ],
            'user_id' => [
                'required',
            ],
            'is_leader' => [
                'required',
                'in:0,1'
            ],
        ];
    }

    public function messages()
    {
        return [
            'project_id.required' => '请指定项目Id！',
            'project_id.exists'   => '请指定有效的项目！',
            'user_id.required' => '请指定会员！',
            'is_leader.required' => '请设置是否为管理员！',
            'is_leader.in' => '请设置有效的管理员标识！',
        ];
    }
}
