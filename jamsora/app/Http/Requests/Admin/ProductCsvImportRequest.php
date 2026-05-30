<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class ProductCsvImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $maxKb = (int) config('jamsora.import.max_upload_kb', 102400);

        return [
            'csv_file' => [
                'required',
                File::types(config('jamsora.import.allowed_mimes', ['csv', 'txt']))
                    ->max($maxKb),
            ],
            'fresh' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        $maxMb = round((int) config('jamsora.import.max_upload_kb', 102400) / 1024, 1);

        return [
            'csv_file.required' => 'Please choose a CSV file to upload.',
            'csv_file.max' => "The CSV file may not be larger than {$maxMb} MB.",
        ];
    }
}
