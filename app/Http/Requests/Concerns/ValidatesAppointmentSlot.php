<?php

namespace App\Http\Requests\Concerns;

use App\Models\Appointment;
use App\Support\AppointmentSchedule;
use Carbon\CarbonImmutable;
use Illuminate\Validation\Validator;

trait ValidatesAppointmentSlot
{
    /**
     * Comprueba que la fecha y la hora pertenezcan a la agenda de la clínica.
     */
    protected function validateAppointmentSlot(Validator $validator, ?int $ignoreId = null): void
    {
        if ($validator->errors()->isNotEmpty()) {
            return;
        }

        $fecha = CarbonImmutable::createFromFormat(
            'Y-m-d',
            (string) $this->input('fecha'),
            AppointmentSchedule::timezone(),
        );

        if ($fecha === null) {
            return;
        }

        $fecha = $fecha->startOfDay();

        if ($fecha->lt(AppointmentSchedule::today())) {
            $validator->errors()->add('fecha', 'La fecha seleccionada ya pasó. Elige un día disponible.');

            return;
        }

        $hora = (string) $this->input('hora');

        if (! in_array($hora, AppointmentSchedule::slotsFor($fecha), true)) {
            $validator->errors()->add('hora', 'La hora seleccionada está fuera del horario de atención.');

            return;
        }

        if (
            $fecha->isSameDay(AppointmentSchedule::today())
            && $hora <= CarbonImmutable::now(AppointmentSchedule::timezone())->format('H:i')
        ) {
            $validator->errors()->add('hora', 'Ese horario ya pasó. Elige otro, por favor.');

            return;
        }

        if (Appointment::isSlotTaken($fecha->toDateString(), $hora, $ignoreId)) {
            $validator->errors()->add('hora', 'Ese horario acaba de ser reservado. Elige otro, por favor.');
        }
    }
}
