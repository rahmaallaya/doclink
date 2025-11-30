<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * L'utilisateur a-t-il le droit de faire cette requête ?
     */
    public function authorize(): bool
    {
        return true; // Tout utilisateur connecté peut modifier son profil
    }

    /**
     * Règles de validation
     */
    public function rules(): array
    {
        return [
            'name'  => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore(auth()->id()), // email unique SAUF pour soi-même
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'], // 2 Mo max
        ];
    }

    /**
     * Messages d'erreur personnalisés en français
     */
    public function messages(): array
    {
        return [
            'name.required'     => 'Ton nom est obligatoire.',
            'name.max'          => 'Ton nom est trop long.',
            'email.required'    => 'L’email est obligatoire.',
            'email.email'       => 'Ce n’est pas une adresse email valide.',
            'email.unique'      => 'Cet email est déjà utilisé par un autre compte.',
            'avatar.image'      => 'Le fichier doit être une image.',
            'avatar.max'        => 'L’image ne doit pas dépasser 2 Mo.',
            'avatar.mimes'      => 'Formats autorisés : jpeg, png, jpg, gif, webp.',
        ];
    }
}