<?php

namespace App\Http\Requests;

use App\Support\AppointmentSchedule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TreatmentResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $isCreating = $this->route('result') === null;

        return [
            'titulo' => ['required', 'string', 'min:3', 'max:120'],
            'descripcion' => ['required', 'string', 'min:10', 'max:600'],
            'servicio' => ['nullable', 'string', Rule::in(AppointmentSchedule::SERVICES)],
            'imagen_antes' => [
                $isCreating ? 'required' : 'nullable',
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:2048',
            ],
            'imagen_despues' => [
                $isCreating ? 'required' : 'nullable',
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:2048',
            ],
            'visible' => ['required', 'boolean'],
            'orden' => ['nullable', 'integer', 'min:0', 'max:999'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'titulo' => is_string($this->input('titulo'))
                ? trim($this->input('titulo'))
                : $this->input('titulo'),
            'descripcion' => is_string($this->input('descripcion'))
                ? trim($this->input('descripcion'))
                : $this->input('descripcion'),
            'servicio' => $this->filled('servicio') ? $this->input('servicio') : null,
        ]);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'titulo.required' => 'Escribe un título para el resultado.',
            'titulo.min' => 'El título debe tener al menos 3 caracteres.',
            'titulo.max' => 'El título no puede superar los 120 caracteres.',
            'descripcion.required' => 'Escribe una breve descripción del tratamiento.',
            'descripcion.min' => 'La descripción debe tener al menos 10 caracteres.',
            'descripcion.max' => 'La descripción no puede superar los 600 caracteres.',
            'servicio.in' => 'Selecciona un servicio válido.',
            'imagen_antes.required' => 'Adjunta la imagen del antes.',
            'imagen_antes.image' => 'El archivo del antes debe ser una imagen.',
            'imagen_antes.mimes' => 'Usa una imagen JPG, PNG o WEBP para el antes.',
            'imagen_antes.max' => 'La imagen del antes no puede superar los 2 MB.',
            'imagen_despues.required' => 'Adjunta la imagen del después.',
            'imagen_despues.image' => 'El archivo del después debe ser una imagen.',
            'imagen_despues.mimes' => 'Usa una imagen JPG, PNG o WEBP para el después.',
            'imagen_despues.max' => 'La imagen del después no puede superar los 2 MB.',
            'visible.required' => 'Indica si el resultado se publica en el sitio.',
            'orden.integer' => 'El orden debe ser un número entero.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'titulo' => 'título',
            'descripcion' => 'descripción',
            'servicio' => 'servicio',
            'imagen_antes' => 'imagen del antes',
            'imagen_despues' => 'imagen del después',
            'visible' => 'visibilidad',
            'orden' => 'orden',
        ];
    }
}
