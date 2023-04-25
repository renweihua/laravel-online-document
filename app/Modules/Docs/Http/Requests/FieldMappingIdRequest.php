<?php

namespace App\Modules\Docs\Http\Requests;

use App\Http\Requests\BaseRequest;
use App\Models\Docs\FieldMapping;
use Illuminate\Validation\Rule;

class FieldMappingIdRequest extends BaseRequest
{
    public function setInstance()
    {
        $this->instance = FieldMapping::getInstance();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => [
                'required',
                Rule::exists($this->instance->getTable(), $this->instance->getKeyName()),
            ],
        ];
    }

    public function messages()
    {
        return [
            'id.required' => '请输入字段映射Id！',
            'id.exists'   => '请指定有效的字段映射！',
        ];
    }
}
