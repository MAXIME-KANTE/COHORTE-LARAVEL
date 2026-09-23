<?php

namespace App\Actions\Fortify;

use App\Models\Promotion;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'code_invitation' => ['required', 'string', 'exists:promotions,code_invitation'],
        ])->validate();

        $promotion = Promotion::where('code_invitation', $input['code_invitation'])->first();

        if (!$promotion->ouverte) {
            throw new \Exception("Cette promotion n'accepte plus de nouveaux membres.");
        }

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'promotion_id' => $promotion->id,
            'role' => 'apprenant',
            'points' => 0,
        ]);
    }
}
