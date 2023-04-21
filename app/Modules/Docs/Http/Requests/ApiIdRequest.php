<?php

namespace App\Modules\Docs\Http\Requests;

use App\Http\Requests\BaseRequest;
use App\Models\Docs\Api;
use Illuminate\Validation\Rule;

class ApiIdRequest extends BaseRequest
{
    public function setInstance()
    {
        $this->instance = Api::getInstance();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'api_id' => [
                'required',
                Rule::exists($this->instance->getTable(), $this->instance->getKeyName()),
            ],
        ];
    }

    public function messages()
    {
        return [
            'api_id.required' => '请指定API！',
            'api_id.exists'   => '请指定有效的API！',
        ];
    }
}
