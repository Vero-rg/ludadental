<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Appointment;
use App\Models\TreatmentResult;
use App\Support\AppointmentSchedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AppointmentController extends Controller
{
    /**
     * Landing pública con el calendario de citas.
     */
    public function index(): Response
    {
        $today = AppointmentSchedule::today();

        return Inertia::render('Home', [
            'services' => AppointmentSchedule::SERVICES,
            'weeklySlots' => AppointmentSchedule::weeklySlots(),
            'openingHours' => AppointmentSchedule::openingHours(),
            'occupiedSlots' => Appointment::occupiedSlots(
                $today->toDateString(),
                $today->addMonths(3)->toDateString(),
            ),
            'clinic' => config('clinic'),
            'results' => TreatmentResult::gallery(),
            'today' => $today->toDateString(),
        ]);
    }

    /**
     * Registra una cita solicitada desde la landing.
     */
    public function store(StoreAppointmentRequest $request): RedirectResponse|JsonResponse
    {
        $data = $request->validated();
        $data['hora'] = substr((string) $data['hora'], 0, 5).':00';

        $appointment = Appointment::create([
            ...$data,
            'estado' => Appointment::ESTADO_PENDIENTE,
        ]);

        $payload = $appointment->toAgendaArray();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Cita registrada correctamente.',
                'appointment' => $payload,
            ], 201);
        }

        Inertia::flash('appointment', $payload);

        return back();
    }
}
