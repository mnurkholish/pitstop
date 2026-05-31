<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'user';
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'plate_number' => ['required', 'string', 'max:255'],
            'vehicle_type' => ['required', 'string', 'max:255'],
            'vehicle_model' => ['required', 'string', 'max:255'],
            'service_date' => ['required', 'date_format:Y-m-d'],
            'arrival_time' => ['required', 'date_format:H:i'],
            'slot' => ['required', Rule::in(['A', 'B', 'C'])],
            'services' => ['required', 'array', 'min:1'],
            'services.*' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('services', 'id')->where('is_active', true),
            ],
            'notes' => ['nullable', 'string'],
        ];
    }
}
