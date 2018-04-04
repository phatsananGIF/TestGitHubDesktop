<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class RsyslogRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'namefile'=>'required',
            'length'=>'required',
        ];
    }

    public function messages()
    {
        return[
            'namefile.required'=>'Please select a file.',
            'length.required'=>'Please select a length.',
            
        ];
    }
}
