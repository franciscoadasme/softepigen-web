<?php

namespace App\Http\Requests;

use App\Helpers\FASTAHelper;
use \Closure;
use Illuminate\Foundation\Http\FormRequest;

class StoreJobSubmissionRequest extends FormRequest
{
    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'amplicon_size_min' => 'amplicon size',
            'amplicon_size_max' => 'amplicon size',
            'primer_size_min' => 'primer size',
            'primer_size_max' => 'primer size',
            'cpg_min' => 'number of CpGs',
            'cpg_max' => 'number of CpGs',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'amplicon_size_max.gt' =>
                'The upper bound of :attribute must be greater than the lower bound.',
            'primer_size_max.gt' =>
                'The upper bound of :attribute must be greater than the lower bound.',
            'cpg_max.gt' =>
                'The upper bound of :attribute must be greater than the lower bound.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'fasta' => [
                'required',
                'file',
                'mimes:txt,fasta,fa',
                'max:512000', # 500MB
                function (string $attribute, mixed $value, Closure $fail) {
                    if (!FASTAHelper::isValid($value)) {
                        $fail(
                            'The contents of the file is not valid nucleic FASTA.',
                        );
                    }
                },
            ],
            'amplicon_size_min' => 'required|integer|min:1',
            'amplicon_size_max' =>
                'required|integer|min:1|gt:amplicon_size_min',
            'primer_size_min' => 'required|integer|min:1',
            'primer_size_max' => 'required|integer|min:1|gt:primer_size_min',
            'cpg_min' => 'required|integer|min:1',
            'cpg_max' => 'required|integer|min:1|gt:cpg_min',
            'astringent' => 'boolean',
        ];
    }
}
