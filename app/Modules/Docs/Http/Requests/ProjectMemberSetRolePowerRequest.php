<?php

namespace App\Modules\Docs\Http\Requests;

use App\Http\Requests\BaseRequest;
use App\Models\Docs\Project;
use Illuminate\Validation\Rule;

class ProjectMemberSetRolePowerRequest extends BaseRequest
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
            'role_power' => [
                'required',
            ],
        ];
    }

    public function messages()
    {
        return [
            'project_id.required' => '请指定项目Id！',
            'project_id.exists'   => '请指定有效的项目！',
            'user_id.required' => '请指定会员！',
            'role_power.required' => '请设置权限！',
        ];
    }
}
