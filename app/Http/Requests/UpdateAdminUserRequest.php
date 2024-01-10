<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminUserRequest extends FormRequest
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
        $id = $this->route('admin_user'); //admin_user is in admin/admin-user/{admin_user}/edit
            return [
                "name" => "required",
                "email" => "required|email|unique:admin_users,email," . $id,
                "phone" => "required|min:9|max:11|unique:admin_users,phone," . $id,
            ];
    }
}
