<?php

namespace App\Modules\Docs\Http\Requests;

use App\Http\Requests\BaseRequest;
use App\Models\Docs\Doc;
use Illuminate\Validation\Rule;

class DocIdRequest extends BaseRequest
{
    public function setInstance()
    {
        $this->instance = Doc::getInstance();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'doc_id' => [
                'required',
                Rule::exists($this->instance->getTable(), $this->instance->getKeyName()),
            ],
        ];
    }

    public function messages()
    {
        return [
            'doc_id.required' => '请输入文档Id！',
            'doc_id.exists'   => '请指定有效的文档！',
        ];
    }
}
