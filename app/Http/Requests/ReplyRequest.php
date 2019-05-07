<?php

namespace App\Http\Requests;

class ReplyRequest extends Request
{
    public function rules()
    {
       return [
           'content'=>'required|min:2'
       ];
    }

    public function messages()
    {
        return [
            // Validation messages
            'content.required'=>'评论不能为为空',
            'content.min'=>'评论最少两个字符'
        ];
    }
}
