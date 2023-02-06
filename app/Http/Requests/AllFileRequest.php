<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QRFileRequest extends FormRequest
{
    private $image_ext = ['jpg', 'jpeg', 'png'];
    private $document_ext = ['pdf'];

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
        $max_size = (int)ini_get('upload_max_filesize') * 1024;
        $all_ext = implode(',', $this->allExtensions());

        return [
            'file.*' => 'required|file|mimes:' . $all_ext . '|max:' . $max_size
        ];
    }

    public function attributes()
    {
        $customAttributes = [];
        foreach ($this->file('file') as $key => $file) {
            $customAttributes['file.' . $key] = $file->getClientOriginalName();
        }

        return $customAttributes;
    }

    /**
     * Get all extensions
     * @return array Extensions of all file types
     */
    private function allExtensions()
    {
        return array_merge($this->image_ext, $this->document_ext);
    }
}
