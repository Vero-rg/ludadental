<?php

namespace App\Support;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

/**
 * Horarios de atención y catálogo de servicios de la clínica.
 *
 * Es la única fuente de verdad para la agenda: la usan las validaciones del
 * backend y se comparte con el frontend para construir el calendario.
 */
final class AppointmentSchedule
{
    /** @var list<string> */
    public const SERVICES = [
        'Ortodoncia',
        'Endodoncia',
        'Rehabilitación Oral',
        'Blanqueamiento',
        'Odontopediatría',
        'Cirugía Oral',
        'Periodoncia',
    ];

    /**
     * Hora de apertura y cierre por día de la semana (0 = domingo).
     *
     * @var array<int, array{0: int, 1: int}|null>
     */
    public const OPENING_HOURS = [
        CarbonInterface::SUNDAY => null,
        CarbonInterface::MONDAY => [9, 17],
        CarbonInterface::TUESDAY => [9, 17],
        CarbonInterface::WEDNESDAY => [9, 17],
        CarbonInterface::THURSDAY => [9, 17],
        CarbonInterface::FRIDAY => [9, 17],
        CarbonInterface::SATURDAY => [9, 13],
    ];

    /**
     * Horarios disponibles (cada hora) para una fecha concreta.
     *
     * @return list<string>
     */
    public static function slotsFor(CarbonInterface $date): array
    {
        return self::weeklySlots()[$date->dayOfWeek] ?? [];
    }

    /**
     * Horarios disponibles agrupados por día de la semana.
     *
     * @return array<int, list<string>>
     */
    public static function weeklySlots(): array
    {
        $slots = [];

        foreach (self::OPENING_HOURS as $weekday => $hours) {
            if ($hours === null) {
                $slots[$weekday] = [];

                continue;
            }

            [$opens, $closes] = $hours;

            $slots[$weekday] = array_map(
                static fn (int $hour): string => sprintf('%02d:00', $hour),
                range($opens, $closes),
            );
        }

        return $slots;
    }

    /**
     * Horarios de atención con etiquetas listas para mostrar.
     *
     * @return list<array{days: string, time: string, open: bool}>
     */
    public static function openingHours(): array
    {
        $weekdays = self::weeklySlots();
        $weekdayHours = self::formatRange($weekdays[CarbonInterface::MONDAY] ?? []);
        $saturdayHours = self::formatRange($weekdays[CarbonInterface::SATURDAY] ?? []);

        return [
            ['days' => 'Lunes a Viernes', 'time' => $weekdayHours, 'open' => $weekdayHours !== 'Cerrado'],
            ['days' => 'Sábados', 'time' => $saturdayHours, 'open' => $saturdayHours !== 'Cerrado'],
            ['days' => 'Domingos', 'time' => 'Cerrado', 'open' => false],
        ];
    }

    public static function today(): CarbonImmutable
    {
        return CarbonImmutable::now(self::timezone())->startOfDay();
    }

    public static function timezone(): string
    {
        return (string) config('clinic.timezone', 'America/Mexico_City');
    }

    /**
     * @param  list<string>  $slots
     */
    private static function formatRange(array $slots): string
    {
        if ($slots === []) {
            return 'Cerrado';
        }

        $opens = ltrim(explode(':', $slots[0])[0], '0');
        $closes = ltrim(explode(':', $slots[count($slots) - 1])[0], '0');

        return sprintf('%s:00 - %s:00', $opens, $closes);
    }
}
