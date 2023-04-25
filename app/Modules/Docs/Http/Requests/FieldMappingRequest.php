<?php

namespace App\Modules\Docs\Http\Requests;

use App\Http\Requests\BaseRequest;
use App\Models\Docs\Project;
use Illuminate\Validation\Rule;

class FieldMappingRequest extends BaseRequest
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
            'field_name' => [
                'required',
            ],
            'field_type' => [
                'required',
            ],
        ];
    }

    public function messages()
    {
        return [
            'project_id.required' => '请指定项目Id！',
            'project_id.exists'   => '请指定有效的项目！',
            'field_name.required' => '请输入字段名称！',
            'field_type.required' => '请选择字段的数据类型！',
        ];
    }
}
