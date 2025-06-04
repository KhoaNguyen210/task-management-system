<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnnualEvaluationReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && in_array(auth()->user()->role, ['Dean', 'Department Head']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'academic_year' => 'nullable|integer|min:2000|max:2100',
            'department_id' => 'nullable|exists:departments,department_id',
            'lecturer_id' => 'nullable|exists:users,user_id',
            'sort_by' => 'nullable|in:name,total_tasks,completion_rate,average_evaluation',
            'sort_order' => 'nullable|in:asc,desc',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'academic_year.integer' => 'Năm học phải là số nguyên.',
            'academic_year.min' => 'Năm học phải từ 2000 trở lên.',
            'academic_year.max' => 'Năm học không được vượt quá 2100.',
            'department_id.exists' => 'Bộ môn không hợp lệ.',
            'lecturer_id.exists' => 'Giảng viên không hợp lệ.',
            'sort_by.in' => 'Tiêu chí sắp xếp không hợp lệ.',
            'sort_order.in' => 'Thứ tự sắp xếp không hợp lệ.',
        ];
    }
}