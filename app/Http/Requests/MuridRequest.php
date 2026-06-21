<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MuridRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('murid');
        
        $rules = [
            'nis' => 'required|string|unique:murid,nis,' . $id,
            'nisn' => 'nullable|string|unique:murid,nisn,' . $id,
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'kelas_id' => 'required|exists:kelas,id',
            'angkatan' => 'required|integer|min:2000|max:2100',
            'status' => 'required|in:aktif,lulus,pindah,keluar',
        ];

        if (!$id) {
            $rules['password'] = 'required|string|min:6';
        } else {
            $rules['password'] = 'nullable|string|min:6';
        }

        return $rules;
    }
}
