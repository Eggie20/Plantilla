<?php
   
namespace App\Http\Requests;
  
use Illuminate\Foundation\Http\FormRequest;
  
class MOUpdateRequest extends FormRequest
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
     * @return array|string>
     */
    public function rules(): array
    {
        return [
            'item_no' => 'required',
            'position_title' => 'required',
            'salary_grade' => 'required',
            'authorized_salary' => 'required',
            'actual_salary' => 'required',
            'step' => 'required',
            'area_code' => 'required',
            'area_type' => 'required',
            'level' => 'required',
            'last_name' => 'required',
            'first_name' => 'required',
            'middle_name' => 'required',
            'date_of_birth' => 'required|date',
            'date_of_original_appointment' => 'required|date',
            'date_of_last_promotion' => 'required|date',
            'status' => 'required',
        ];
    }
}