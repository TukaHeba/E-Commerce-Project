<?php

namespace App\Http\Requests\Photo;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreMultiplePhotosRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     * This method is called before validation starts to clean or normalize inputs.
     * 
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            //
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'photos' => ['required', 'array', 'min:1'], // Ensure it's an array and at least one photo is provided.
            'photos.*' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:8192'], // Validate each photo in the array.
        ];
    }

     /**
     * Define human-readable attribute names for validation errors.
     * 
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            //
        ];
    }

    /**
     * Define custom error messages for validation failures.
     * 
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'photos.required' => 'You must upload at least one photo.',
            'photos.array' => 'The photos field must be an array.',
            'photos.*.image' => 'Each file must be a valid image.',
            'photos.*.mimes' => 'Each photo must be a file of type: jpeg, png, jpg, gif , webp.',
            'photos.*.max' => 'Each photo must not exceed 8MB in size.',
        ];
    }

    /**
     * Handle validation errors and throw an exception.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator The validation instance.
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->all();
        throw new HttpResponseException(
            response()->json([
                'status' => 'error',
                'message' => 'A server error has occurred',
                'errors' => $errors,
            ], 403)
        );
    }
}
