<?php

namespace App\Http\Requests\Settings;

use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    use ProfileValidationRules;

    protected function prepareForValidation(): void
    {
        $this->merge([
            'last_name' => $this->input('last_name', $this->input('lastName')),
            'first_name' => $this->input('first_name', $this->input('firstName')),
            'phone_number' => $this->input('phone_number', $this->input('phoneNumber')),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->user()?->idUser ?? $this->user()?->id;

        // Autoriser les mises à jour partielles: les champs sont validés uniquement s'ils sont fournis
        return array_merge($this->profileRules($userId), [
            'last_name'    => ['sometimes', 'string', 'max:255'],
            'first_name'   => ['sometimes', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'sex'          => ['nullable', 'string', Rule::in(['Homme', 'Femme', 'Autre'])],
            'birthday'     => ['nullable', 'date'],
            'address'      => ['nullable', 'string', 'max:500'],
            'numPiece'     => ['nullable', 'string', 'max:255'],
            'photoId'      => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'scanPiece'    => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
        ]);
    }
}
