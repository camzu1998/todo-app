<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\TaskStatus;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', Rule::enum(TaskStatus::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Tytuł jest wymagany',
            'title.max' => 'Tytuł nie może przekraczać 255 znaków',
            'status.enum' => 'Nieprawidłowy status zadania',
        ];
    }
}
