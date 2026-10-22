<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesAppointmentSlot;
use App\Models\Appointment;
use App\Support\AppointmentSchedule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreAppointmentRequest extends FormRequest
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
            'paciente_nombre' => ['required', 'string', 'min:3', 'max:120'],
            'paciente_telefono' => ['required', 'string', 'regex:/^\d{10}$/'],
            'paciente_email' => ['nullable', 'string', 'email', 'max:150'],
            'servicio' => ['required', 'string', Rule::in(AppointmentSchedule::SERVICES)],
            'fecha' => ['required', 'date_format:Y-m-d'],
            'hora' => ['required', 'date_format:H:i'],
            'tipo_paciente' => ['required', 'string', Rule::in(Appointment::TIPOS_PACIENTE)],
            'notas' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'paciente_nombre' => is_string($this->input('paciente_nombre'))
                ? trim($this->input('paciente_nombre'))
                : $this->input('paciente_nombre'),
            'paciente_telefono' => $this->normalizePhone($this->input('paciente_telefono')),
            'paciente_email' => $this->filled('paciente_email')
                ? mb_strtolower(trim((string) $this->input('paciente_email')))
                : null,
            'notas' => $this->filled('notas') ? trim((string) $this->input('notas')) : null,
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $this->validateAppointmentSlot($validator);
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'paciente_nombre.required' => 'Necesitamos el nombre del paciente.',
            'paciente_nombre.min' => 'Escribe el nombre completo del paciente.',
            'paciente_telefono.required' => 'Necesitamos un teléfono de contacto.',
            'paciente_telefono.regex' => 'El teléfono debe tener 10 dígitos.',
            'paciente_email.email' => 'Escribe un correo electrónico válido.',
            'servicio.required' => 'Selecciona un servicio.',
            'servicio.in' => 'Selecciona un servicio válido.',
            'fecha.required' => 'Selecciona una fecha.',
            'fecha.date_format' => 'La fecha seleccionada no es válida.',
            'hora.required' => 'Selecciona un horario.',
            'hora.date_format' => 'El horario seleccionado no es válido.',
            'tipo_paciente.required' => 'Indica si la cita es para un niño o un adulto.',
            'tipo_paciente.in' => 'Indica si la cita es para un niño o un adulto.',
            'notas.max' => 'Las notas no pueden superar los 1000 caracteres.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'paciente_nombre' => 'nombre del paciente',
            'paciente_telefono' => 'teléfono',
            'paciente_email' => 'correo electrónico',
            'servicio' => 'servicio',
            'fecha' => 'fecha',
            'hora' => 'horario',
            'tipo_paciente' => 'tipo de paciente',
            'notas' => 'notas',
        ];
    }

    private function normalizePhone(mixed $value): mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        $digits = preg_replace('/\D+/', '', $value) ?? '';

        if (strlen($digits) === 12 && str_starts_with($digits, '52')) {
            $digits = substr($digits, 2);
        }

        if (strlen($digits) === 13 && str_starts_with($digits, '521')) {
            $digits = substr($digits, 3);
        }

        return $digits;
    }
}
