<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClubRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'CLU_NOM' => ['required', 'string', 'max:255'],
            'CLU_ADRESSE' => ['nullable', 'string', 'max:255'],
            'CLU_VILLE' => ['nullable', 'string', 'max:100'],
            'CLU_CODE_POSTAL' => ['nullable', 'string', 'max:20'],
        ];
    }
}
