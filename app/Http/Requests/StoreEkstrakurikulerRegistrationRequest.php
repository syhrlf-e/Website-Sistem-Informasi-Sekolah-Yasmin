<?php
namespace App\Http\Requests;
use App\Models\Ekstrakurikuler;
use App\Models\EkstrakurikulerRegistration;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
class StoreEkstrakurikulerRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Public endpoint
    }
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'ekstrakurikuler_id' => 'required|exists:ekstrakurikuler,id',
            'registration_token' => 'required|string',
            'nama_lengkap' => 'required|string|min:3|max:255',
            'kelas' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'no_whatsapp' => [
                'required',
                'string',
                'regex:/^(\+62|62|0)[0-9]{9,12}$/',
            ],
            'alasan_bergabung' => 'required|string|min:20|max:500',
        ];
    }
    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'ekstrakurikuler_id.required' => 'Ekstrakurikuler harus dipilih',
            'ekstrakurikuler_id.exists' => 'Ekstrakurikuler tidak ditemukan',
            'registration_token.required' => 'Token pendaftaran harus diisi',
            'nama_lengkap.required' => 'Nama lengkap harus diisi',
            'nama_lengkap.min' => 'Nama lengkap minimal 3 karakter',
            'kelas.required' => 'Kelas harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'no_whatsapp.required' => 'Nomor WhatsApp harus diisi',
            'no_whatsapp.regex' => 'Format nomor WhatsApp tidak valid (contoh: 08123456789 atau 628123456789)',
            'alasan_bergabung.required' => 'Alasan bergabung harus diisi',
            'alasan_bergabung.min' => 'Alasan bergabung minimal 20 karakter',
            'alasan_bergabung.max' => 'Alasan bergabung maksimal 500 karakter',
        ];
    }
    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate token
            $this->validateToken($validator);
            
            // Check for duplicate registration
            $this->checkDuplicateRegistration($validator);
        });
    }
    /**
     * Validate registration token
     */
    protected function validateToken($validator)
    {
        $ekstrakurikuler = Ekstrakurikuler::find($this->ekstrakurikuler_id);
        
        if (!$ekstrakurikuler) {
            return;
        }
        // Check if token exists
        if (!$ekstrakurikuler->registration_token) {
            $validator->errors()->add(
                'registration_token',
                'Token pendaftaran belum diaktifkan untuk ekstrakurikuler ini'
            );
            return;
        }
        // Check if token matches
        if ($ekstrakurikuler->registration_token !== $this->registration_token) {
            $validator->errors()->add(
                'registration_token',
                'Token tidak valid atau sudah kadaluarsa'
            );
            return;
        }
        // Check if token has expired (24 hours)
        if ($ekstrakurikuler->token_expires_at && Carbon::now()->isAfter($ekstrakurikuler->token_expires_at)) {
            $validator->errors()->add(
                'registration_token',
                'Token tidak valid atau sudah kadaluarsa'
            );
            return;
        }
    }
    /**
     * Check for duplicate registration
     */
    protected function checkDuplicateRegistration($validator)
    {
        $exists = EkstrakurikulerRegistration::where('email', $this->email)
            ->where('ekstrakurikuler_id', $this->ekstrakurikuler_id)
            ->exists();
        if ($exists) {
            $validator->errors()->add(
                'email',
                'Anda sudah pernah mendaftar untuk ekstrakurikuler ini'
            );
        }
    }
}
