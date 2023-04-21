<?php

namespace App\Modules\Docs\Http\Requests;

use App\Http\Requests\BaseRequest;
use App\Models\Docs\Group;
use App\Models\Docs\Project;
use Illuminate\Validation\Rule;

class GroupRequest extends BaseRequest
{
    protected $projectModel;

    public function setInstance()
    {
        $this->instance = Group::getInstance();
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
            'group_type' => [
                'required',
            ],
            'group_name' => [
                'required',
            ],
        ];
    }

    public function messages()
    {
        return [
            'project_id.required' => '请指定项目Id！',
            'project_id.exists'   => '请指定有效的项目！',
            'group_type.required' => '请设置分组类型！',
            'group_name.required' => '请输入分组名称！',
        ];
    }
}
