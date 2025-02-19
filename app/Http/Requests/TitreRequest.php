<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TitreRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
                'exercice' => ['required', 'int','min_digits:4','max_digits:4' ],
                'nom' => ['required', 'string', 'max:255'],
                'localisation' => ['required', 'string', 'max:255'],
                'zone_id' => ['required', 'int','exists:zones,id'],
                'essence_id' => ['required', 'int','exists:essences,id'],
                'forme_id' => ['required', 'int','exists:formes,id'],
                'type_id' => ['required', 'int','exists:types,id'],
                'volume' => ['required', 'numeric' ],
        ];
    }
}
