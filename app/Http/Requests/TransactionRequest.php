<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
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
            'date' => ['required', 'date'],
            'exercice' => ['required', 'int', 'min_digits:4', 'max_digits:4'],
            'numero' => ['required', 'numeric', 'min:0'],
            'titre_id' => ['required', 'int', 'exists:titres,id'],
            'type_id' => ['required', 'int', 'exists:types,id'],
            'essence_id' => ['required', 'int', 'exists:essences,id'],
            'forme_id' => ['required', 'int', 'exists:formes,id'],
            'conditionnemment_id' => ['required', 'int', 'exists:conditionnemments,id'],
            'societe_id' => ['required', 'int', 'exists:societes,id'],
            'pays' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'volume' => ['required', 'numeric','min:0'],
        ];
    }
}
