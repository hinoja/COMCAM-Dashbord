<?php

namespace App\Imports;

use App\Models\Essence;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class EssenceImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows, WithBatchInserts, WithChunkReading
{
    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Essence([
            'nom_local' => $row['nom_local'],
            'code' => $row['code'],
        ]);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'nom_local' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'unique:essences,code'],
        ];
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'nom_local.required' => 'Le nom local est obligatoire',
            'code.required' => 'Le code est obligatoire',
            'code.unique' => 'Ce code existe déjà',
        ];
    }

    /**
     * @return int
     */
    public function batchSize(): int
    {
        return 1000;
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 1000;
    }
}

