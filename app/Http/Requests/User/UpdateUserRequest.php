<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Password;
use Propaganistas\LaravelPhone\Rules\Phone;



class UpdateUserRequest extends FormRequest
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
            'first_name' => $this->first_name ? ucwords(trim($this->first_name)) : null,
            'last_name' => $this->last_name ? ucwords(trim($this->last_name)) : null,
            'email' => $this->email ? strtolower(trim($this->email)) : null,
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
            'first_name' => ['nullable', 'string', 'max:50', 'regex:/^[a-zA-Z\s\'-]+$/'],
            'last_name' => ['nullable', 'string', 'max:50', 'regex:/^[a-zA-Z\s\'-]+$/'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email,' . $this->user->id],
            'password' => ['nullable', 'max:30', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised()],
            'phone' => ['nullable', new Phone],
            'address' => ['nullable', 'string', 'max:255'],
            'is_male' => ['nullable', 'boolean'],
            'birthdate' => ['nullable', 'date', 'before:today'],
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
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email Address',
            'password' => 'Password',
            'phone' => 'Phone Number',
            'address' => 'Address',
            'is_male' => 'Gender',
            'birthdate' => 'Birthday',
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
            'max' => 'The :attribute may not be greater than :max characters.',
            'unique' => 'The :attribute has already been taken.',
            'date' => 'The :attribute must be a valid date.',
            'regix' => ':attribute must be a valid name contains only letters.',
            'email' => 'email must be a valid email address.',
            'password.confirmed' => 'The password confirmation does not match.',
            'min' => 'The :attribute must be at least :min characters.',
            'password.letters' => 'The password must contain at least one letter.',
            'password.mixedCase' => 'The password must contain both uppercase and lowercase letters.',
            'password.numbers' => 'The password must contain at least one number.',
            'password.symbols' => 'The password must contain at least one special character.',
            'password.uncompromised' => 'The password appears in a data leak, please choose a different one.'
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
            ], status: 422)
        );
    }
}
