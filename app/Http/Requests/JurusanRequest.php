<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JurusanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('jurusan');
        return [
            'kode_jurusan' => 'required|string|unique:jurusan,kode_jurusan,' . $id,
            'nama_jurusan' => 'required|string|max:255',
        ];
    }
}
