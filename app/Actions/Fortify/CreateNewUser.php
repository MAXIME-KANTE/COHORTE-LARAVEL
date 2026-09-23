<?php

namespace App\Actions\Fortify;

use App\Models\Promotion;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'password' => $this->passwordRules(),
            'code_invitation' => ['required', 'string', 'max:12'],
        ], [
            'code_invitation.required' => 'Le code d’invitation de votre promotion est obligatoire.',
        ])->validate();

        $promotion = Promotion::where('code_invitation', $input['code_invitation'])->first();

        if (! $promotion) {
            throw ValidationException::withMessages([
                'code_invitation' => 'Ce code d’invitation n’existe pas.',
            ]);
        }

        if (! $promotion->ouverte) {
            throw ValidationException::withMessages([
                'code_invitation' => 'Les inscriptions à cette promotion sont closes.',
            ]);
        }

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'promotion_id' => $promotion->id,
            'role' => 'apprenant',
        ]);
    }
}
