<?php

namespace App\Modules\Docs\Http\Requests;

use App\Http\Requests\BaseRequest;
use App\Models\Docs\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectIdRequest extends BaseRequest
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
            'project_id' => [
                'required',
                Rule::exists($this->instance->getTable(), $this->instance->getKeyName()),
            ],
        ];
    }

    public function messages()
    {
        return [
            'project_id.required' => '请输入项目Id！',
            'project_id.exists'   => '请指定有效的项目！',
        ];
    }
}
