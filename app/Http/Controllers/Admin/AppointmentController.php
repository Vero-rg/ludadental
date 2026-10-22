<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RescheduleAppointmentRequest;
use App\Http\Requests\UpdateAppointmentStatusRequest;
use App\Models\Appointment;
use App\Support\AppointmentSchedule;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AppointmentController extends Controller
{
    /**
     * Agenda del consultorio con filtros y métricas rápidas.
     */
    public function index(Request $request): Response
    {
        $filters = [
            'estado' => (string) $request->query('estado', 'todas'),
            'fecha' => (string) $request->query('fecha', ''),
            'q' => trim((string) $request->query('q', '')),
        ];

        if ($filters['fecha'] !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $filters['fecha']) !== 1) {
            $filters['fecha'] = '';
        }

        $today = AppointmentSchedule::today();
        $weekStart = $today->startOfWeek(CarbonInterface::MONDAY);

        $stats = [
            'hoy' => Appointment::query()
                ->whereDate('fecha', $today->toDateString())
                ->where('estado', '!=', Appointment::ESTADO_CANCELADA)
                ->count(),
            'pendientes' => Appointment::query()
                ->where('estado', Appointment::ESTADO_PENDIENTE)
                ->count(),
            'semana' => Appointment::query()
                ->whereBetween('fecha', [$weekStart->toDateString(), $weekStart->addDays(6)->toDateString()])
                ->where('estado', '!=', Appointment::ESTADO_CANCELADA)
                ->count(),
        ];

        $appointments = $this->filteredQuery($filters)
            ->paginate(12)
            ->withQueryString()
            ->through(fn (Appointment $appointment): array => $appointment->toAgendaArray());

        return Inertia::render('Admin/Appointments/Index', [
            'appointments' => $appointments,
            'filters' => $filters,
            'stats' => $stats,
            'services' => AppointmentSchedule::SERVICES,
            'weeklySlots' => AppointmentSchedule::weeklySlots(),
            'occupiedSlots' => Appointment::occupiedSlots(
                $today->toDateString(),
                $today->addMonths(6)->toDateString(),
            ),
            'clinic' => config('clinic'),
            'today' => $today->toDateString(),
        ]);
    }

    /**
     * Cambia el estado de una cita (confirmar, completar o cancelar).
     */
    public function updateStatus(UpdateAppointmentStatusRequest $request, Appointment $appointment): RedirectResponse
    {
        $appointment->update([
            'estado' => (string) $request->validated('estado'),
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => "La cita {$appointment->codigo_folio} se actualizó a {$appointment->estado}.",
        ]);

        return back();
    }

    /**
     * Reagenda una cita a una nueva fecha y hora.
     */
    public function reschedule(RescheduleAppointmentRequest $request, Appointment $appointment): RedirectResponse
    {
        $data = $request->validated();

        $appointment->update([
            'fecha' => (string) $data['fecha'],
            'hora' => substr((string) $data['hora'], 0, 5).':00',
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => "La cita {$appointment->codigo_folio} se reagendó para el "
                .$appointment->fecha->format('d/m/Y')." a las {$appointment->horaCorta()}.",
        ]);

        return back();
    }

    /**
     * @param  array{estado: string, fecha: string, q: string}  $filters
     * @return Builder<Appointment>
     */
    private function filteredQuery(array $filters): Builder
    {
        return Appointment::query()
            ->when(
                $filters['estado'] === 'pendientes',
                fn (Builder $query): Builder => $query->where('estado', Appointment::ESTADO_PENDIENTE),
            )
            ->when(
                $filters['estado'] === 'confirmadas',
                fn (Builder $query): Builder => $query->where('estado', Appointment::ESTADO_CONFIRMADA),
            )
            ->when(
                $filters['estado'] === 'hoy',
                fn (Builder $query): Builder => $query->whereDate('fecha', AppointmentSchedule::today()->toDateString()),
            )
            ->when(
                $filters['fecha'] !== '',
                fn (Builder $query): Builder => $query->whereDate('fecha', $filters['fecha']),
            )
            ->when($filters['q'] !== '', function (Builder $query) use ($filters): void {
                $term = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $filters['q']).'%';
                $digits = preg_replace('/\D+/', '', $filters['q']) ?? '';

                $query->where(function (Builder $query) use ($term, $digits): void {
                    $query->where('paciente_nombre', 'like', $term)
                        ->orWhere('codigo_folio', 'like', $term)
                        ->orWhere('servicio', 'like', $term);

                    if ($digits !== '') {
                        $query->orWhere('paciente_telefono', 'like', "%{$digits}%");
                    }
                });
            })
            ->orderByRaw(
                'CASE WHEN fecha >= ? THEN 0 ELSE 1 END',
                [AppointmentSchedule::today()->toDateString()],
            )
            ->orderBy('fecha')
            ->orderBy('hora');
    }
}
