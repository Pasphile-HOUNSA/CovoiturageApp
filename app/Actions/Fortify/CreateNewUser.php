<?php

namespace App\Actions\Fortify;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    public function create(array $input): User
    {
        Validator::make($input, [
            'firstName'   => ['required', 'string', 'min:2', 'max:255'],
            'lastName'    => ['required', 'string', 'min:2', 'max:255'],
            'sex'         => ['required', 'string', 'in:Homme,Femme,Autre'],
            'birthday'    => ['required', 'date', 'before:18 years ago'],
            'email'       => ['required', 'string', 'email:rfc', 'max:255', 'unique:users,email'],
            'password'    => ['required', 'string', Password::min(8)->mixedCase()->numbers()->symbols()],
            'phoneNumber' => ['required', 'string', 'regex:/^(\+22901|01)\d{8}$/'],
            'address'     => ['required', 'string', 'min:5', 'max:255'],
        ], [
            'firstName.required'   => 'Le prénom est requis.',
            'firstName.min'        => 'Le prénom doit contenir au moins 2 caractères.',
            'lastName.required'    => 'Le nom est requis.',
            'lastName.min'         => 'Le nom doit contenir au moins 2 caractères.',
            'sex.in'               => 'Le sexe doit être Homme, Femme ou Autre.',
            'birthday.required'    => 'La date de naissance est requise.',
            'birthday.before'      => 'Vous devez avoir au moins 18 ans pour vous inscrire.',
            'email.required'       => 'L\'adresse e-mail est requise.',
            'email.email'          => 'L\'adresse e-mail doit avoir un format valide.',
            'email.unique'         => 'Cette adresse e-mail est déjà enregistrée.',
            'password.required'    => 'Le mot de passe est requis.',
            'password.min'         => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.mixed'       => 'Le mot de passe doit contenir au moins une lettre majuscule et une lettre minuscule.',
            'password.numbers'     => 'Le mot de passe doit contenir au moins un chiffre.',
            'password.symbols'     => 'Le mot de passe doit contenir au moins un caractère spécial.',
            'phoneNumber.required' => 'Le numéro de téléphone est requis.',
            'phoneNumber.regex'    => 'Le numéro doit être au format béninois: +229 01XXXXXXXX ou 01XXXXXXXX.',
            'address.required'     => 'L\'adresse est requise.',
            'address.min'          => 'L\'adresse doit être plus précise (5 caractères minimum).',
        ])->validate();

        $user = User::create([
            'firstName'         => $input['firstName'],
            'lastName'          => $input['lastName'],
            'sex'               => $input['sex'],
            'birthday'          => $input['birthday'],
            'email'             => $input['email'],
            'password'          => Hash::make($input['password']),
            'phoneNumber'       => $input['phoneNumber'],
            'address'           => $input['address'],
            'registrationDate'  => now(),
            'lastLogin'         => now(),
        ]);

        $role = Role::where('slug', 'passager')->first();

        if ($role) {
            $user->roles()->attach($role->idRole);
        }

        return $user;
    }
}