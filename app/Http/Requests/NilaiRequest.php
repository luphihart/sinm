<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NilaiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'murid_id' => 'required|exists:murid,id',
            'semester_id' => 'required|exists:semester,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'nilai' => 'required|numeric|min:0|max:100',
        ];
    }
}
