<?php

namespace App\Modules\Docs\Http\Requests;

use App\Http\Requests\BaseRequest;

class GroupBatchSaveRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'groups' => [
                'required',
            ],
        ];
    }

    public function messages()
    {
        return [
            'groups.required' => '请设置分组！',
        ];
    }
}
