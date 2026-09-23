<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->promotion_id !== null;
    }

    public function rules(): array
    {
        return [
            'titre' => ['required', 'string', 'max:150'],
            'contenu' => ['required', 'string', 'min:10', 'max:3000'],
        ];
    }

    public function messages(): array
    {
        return [
            'titre.required' => 'Votre question doit avoir un titre.',
            'contenu.required' => 'Le détail de la question ne peut pas être vide.',
            'contenu.min' => 'Le détail de la question doit faire au moins 10 caractères.',
        ];
    }
}
