<?php

namespace App\Http\Requests\Order;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class IndexOrderRequest extends FormRequest
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
            'shipping_address' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:pending,shipped,delivered,canceled',
            'total_price' => 'nullable|numeric|min:0',
            'show_deleted' => 'nullable|boolean'
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
            'shipping_address' => 'Shipping Address',
            'status' => 'Order Status',
            'total_price' => 'Total Price',
            'show_deleted' => 'Display Deleted Orders'
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
                'message' => 'There is error in validation from request',
                'errors' => $errors,
            ], 403)
        );
    }
}
