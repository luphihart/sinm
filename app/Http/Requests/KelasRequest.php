<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KelasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jurusan_id' => 'required|exists:jurusan,id',
            'nama_kelas' => 'required|string|max:255',
            'tingkat' => 'required|string',
        ];
    }
}
