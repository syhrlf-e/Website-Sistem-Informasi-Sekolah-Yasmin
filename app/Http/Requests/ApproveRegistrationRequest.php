<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class ApproveRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware
    }
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'admin_notes' => 'nullable|string|max:1000',
        ];
    }
    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'admin_notes.max' => 'Catatan admin maksimal 1000 karakter',
        ];
    }
}
