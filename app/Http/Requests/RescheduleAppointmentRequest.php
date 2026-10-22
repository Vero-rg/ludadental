<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesAppointmentSlot;
use App\Models\Appointment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class RescheduleAppointmentRequest extends FormRequest
{
    use ValidatesAppointmentSlot;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'fecha' => ['required', 'date_format:Y-m-d'],
            'hora' => ['required', 'date_format:H:i'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $appointment = $this->route('appointment');

            $this->validateAppointmentSlot(
                $validator,
                $appointment instanceof Appointment ? $appointment->id : null,
            );
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'fecha.required' => 'Selecciona una fecha.',
            'fecha.date_format' => 'La fecha seleccionada no es válida.',
            'hora.required' => 'Selecciona un horario.',
            'hora.date_format' => 'El horario seleccionado no es válido.',
        ];
    }
}
