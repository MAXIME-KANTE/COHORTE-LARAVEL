<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePublicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->promotion_id !== null;
    }

    public function rules(): array
    {
        return [
            'titre' => ['nullable', 'string', 'max:150'],
            'contenu' => ['required', 'string', 'min:10', 'max:3000'],
        ];
    }

    public function messages(): array
    {
        return [
            'contenu.required' => 'Votre publication ne peut pas être vide.',
            'contenu.min' => 'Votre publication doit faire au moins 10 caractères.',
        ];
    }
}
