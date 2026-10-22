<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $uuid
 * @property string $codigo_folio
 * @property string $paciente_nombre
 * @property string $paciente_telefono
 * @property string|null $paciente_email
 * @property string $servicio
 * @property Carbon $fecha
 * @property string $hora
 * @property string $tipo_paciente
 * @property string|null $notas
 * @property string $estado
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'codigo_folio',
    'paciente_nombre',
    'paciente_telefono',
    'paciente_email',
    'servicio',
    'fecha',
    'hora',
    'tipo_paciente',
    'notas',
    'estado',
])]
class Appointment extends Model
{
    public const ESTADO_PENDIENTE = 'pendiente';

    public const ESTADO_CONFIRMADA = 'confirmada';

    public const ESTADO_CANCELADA = 'cancelada';

    public const ESTADO_COMPLETADA = 'completada';

    /** @var list<string> */
    public const ESTADOS = [
        self::ESTADO_PENDIENTE,
        self::ESTADO_CONFIRMADA,
        self::ESTADO_CANCELADA,
        self::ESTADO_COMPLETADA,
    ];

    /** @var list<string> */
    public const ESTADOS_ACTIVOS = [
        self::ESTADO_PENDIENTE,
        self::ESTADO_CONFIRMADA,
        self::ESTADO_COMPLETADA,
    ];

    /** @var list<string> */
    public const TIPOS_PACIENTE = ['adulto', 'nino'];

    protected static function booted(): void
    {
        static::creating(function (Appointment $appointment): void {
            $appointment->uuid ??= (string) Str::uuid();
            $appointment->codigo_folio ??= self::generateFolio();
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha' => 'date:Y-m-d',
        ];
    }

    public static function generateFolio(): string
    {
        $alphabet = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';

        do {
            $code = '';

            for ($i = 0; $i < 5; $i++) {
                $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }

            $folio = 'LUD-'.$code;
        } while (self::query()->where('codigo_folio', $folio)->exists());

        return $folio;
    }

    /**
     * Indica si otro registro activo ya ocupa ese día y hora.
     */
    public static function isSlotTaken(string $fecha, string $hora, ?int $ignoreId = null): bool
    {
        $time = strlen($hora) === 5 ? $hora.':00' : $hora;

        return self::query()
            ->whereDate('fecha', $fecha)
            ->where('hora', $time)
            ->whereNotIn('estado', [self::ESTADO_CANCELADA])
            ->when($ignoreId, fn (Builder $query): Builder => $query->whereKeyNot($ignoreId))
            ->exists();
    }

    /**
     * Horarios ocupados entre dos fechas, para deshabilitarlos en el frontend.
     *
     * @return list<array{fecha: string, hora: string, id: int}>
     */
    public static function occupiedSlots(string $from, string $to): array
    {
        $appointments = self::query()
            ->whereBetween('fecha', [$from, $to])
            ->whereIn('estado', self::ESTADOS_ACTIVOS)
            ->orderBy('fecha')
            ->orderBy('hora')
            ->get(['id', 'fecha', 'hora']);

        $slots = [];

        foreach ($appointments as $appointment) {
            $slots[] = [
                'fecha' => $appointment->fecha->format('Y-m-d'),
                'hora' => $appointment->horaCorta(),
                'id' => $appointment->id,
            ];
        }

        return $slots;
    }

    public function horaCorta(): string
    {
        return substr((string) $this->hora, 0, 5);
    }

    /**
     * Representación plana utilizada por la landing y el panel.
     *
     * @return array<string, mixed>
     */
    public function toAgendaArray(): array
    {
        return [
            'id' => $this->id,
            'codigo_folio' => $this->codigo_folio,
            'paciente_nombre' => $this->paciente_nombre,
            'paciente_telefono' => $this->paciente_telefono,
            'paciente_email' => $this->paciente_email,
            'servicio' => $this->servicio,
            'fecha' => $this->fecha->format('Y-m-d'),
            'hora' => $this->horaCorta(),
            'tipo_paciente' => $this->tipo_paciente,
            'notas' => $this->notas,
            'estado' => $this->estado,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
