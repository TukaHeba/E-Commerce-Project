<?php

namespace App\Http\Requests\Category\MainCategory;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateMainCategoryRequest extends FormRequest
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
            'main_category_name' => $this->input('main_category_name') ? ucwords(trim($this->input('main_category_name'))) : null,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('main_category');

        return [
            'main_category_name' => ['nullable', 'string', 'min:4', 'max:50', Rule::unique('main_categories', 'main_category_name')->ignore($id)],
            'sub_category_name' => 'nullable|array',
            'sub_category_name.*' => 'nullable|exists:sub_categories,id',
            'photos' => 'sometimes|nullable|array|min:1',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:8192',
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
            'main_category_name' => 'main category name',
            'sub_category_name' => 'sub category name',
            'sub_category_name.*' => 'sub category name',
            'photos' => 'photos',
            'photos.*' => 'photo',
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
            'unique' => 'The :attribute has already been taken.',
            'min' => 'The :attribute must be at least :min characters.',
            'max' => 'The :attribute may not be greater than :max characters.',
            'exists' => 'The selected :attribute is invalid.',
            'array' => 'The :attribute should be an array',
            'image' => 'The :attribute must be an image.',
            'mimes' => 'The :attribute must be a file of type: :values.',
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
            ], 422)
        );
    }
}
