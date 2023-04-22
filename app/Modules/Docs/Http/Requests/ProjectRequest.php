<?php

namespace App\Modules\Docs\Http\Requests;

use App\Http\Requests\BaseRequest;
use App\Models\Docs\Project;

class ProjectRequest extends BaseRequest
{
    public function setInstance()
    {
        $this->instance = Project::getInstance();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'project_type' => [
                'required',
            ],
            'project_name' => [
                'required',
            ],
        ];
    }

    public function messages()
    {
        return [
            'project_type.required' => '请设置项目类型！',
            'project_name.required' => '请输入项目名称！',
        ];
    }
}
