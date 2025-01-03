<?php

namespace App\Http\Requests\Product;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProductRequest extends FormRequest
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
            'name'=>$this->name ? strtolower($this->name):null,
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
            'name'=>'nullable|string|min:4|max:100',
            'description'=>'nullable|string',
            'price'=>'nullable|numeric|min:0',
            'product_quantity'=>'nullable|int|min:0',
            'maincategory_subcategory_id'=>'nullable|exists:maincategory_subcategory,id',
            'photosDeleted' => ['nullable', 'array', 'min:1'], // Ensure it's an array and at least one photo is provided.
            'photosDeleted.*' => ['string'],
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
            'name'=>'Product Name',
            'description'=>'Product description',
            'price'=>'Product Price',
            'product_quantity'=>'product quantity',
            'category_id'=>'category_ID',
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
            'required' => 'The :attribute field is required.',
            'max' => 'The :attribute may not be greater than :max characters.',
            'min' => 'The :attribute must be at least :min characters.',
            'unique' => 'The :attribute has already been taken.',
            'in' => 'The selected :attribute is invalid.',
            'date' => 'The :attribute must be a valid date.',
            'exists' => 'The selected :attribute is invalid.',
            'numeric'=>'The :attribute must be a deciaml or integer value',
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
