<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Classe de validation d'un résultat.
 *
 * @author Charles-Olivier Faucher et Benjamin Theriault
 */
class ResultatRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'resultat' => 'required|integer|in:0,1,2,3,4,5,6',
        ];
    }
}
