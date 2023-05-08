<?php

namespace App\Modules\Docs\Http\Requests;

use App\Http\Requests\BaseRequest;
use App\Models\Docs\Group;
use Illuminate\Validation\Rule;

class GroupIdRequest extends BaseRequest
{
    public function setInstance()
    {
        $this->instance = Group::getInstance();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'group_id' => [
                'required',
                Rule::exists($this->instance->getTable(), $this->instance->getKeyName()),
            ],
        ];
    }

    public function messages()
    {
        return [
            'group_id.required' => '请指定分组！',
            'group_id.exists'   => '请指定有效的分组！',
        ];
    }
}
