<?php

namespace App\Modules\Docs\Http\Requests;

use App\Http\Requests\BaseRequest;
use App\Models\Docs\Project;
use Illuminate\Validation\Rule;

class ApiRequest extends BaseRequest
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
            'api_url' => [
                'required',
            ],
            'api_name' => [
                'required',
            ],
            'http_protocol' => [
                'required',
            ],
            'http_method' => [
                'required',
            ],
        ];
    }

    public function messages()
    {
        return [
            'project_id.required' => '请指定项目Id！',
            'project_id.exists'   => '请指定有效的项目！',
            'api_url.required' => '请输入API的URL！',
            'api_name.required' => '请输入API名称！',
            'http_protocol.required' => '请设定请求协议！',
            'http_method.required' => '请设定请求方式！',
        ];
    }
}
