import type { AppointmentStatus, PatientType } from '@/types';

export const MONTH_NAMES = [
    'enero',
    'febrero',
    'marzo',
    'abril',
    'mayo',
    'junio',
    'julio',
    'agosto',
    'septiembre',
    'octubre',
    'noviembre',
    'diciembre',
];

export const WEEKDAY_SHORT = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];

const WEEKDAY_LONG = [
    'domingo',
    'lunes',
    'martes',
    'miércoles',
    'jueves',
    'viernes',
    'sábado',
];

const STATUS_LABELS: Record<AppointmentStatus, string> = {
    pendiente: 'Pendiente',
    confirmada: 'Confirmada',
    cancelada: 'Cancelada',
    completada: 'Completada',
};

const PATIENT_TYPE_LABELS: Record<PatientType, string> = {
    adulto: 'Adulto',
    nino: 'Niño',
};

export function capitalize(value: string): string {
    return value.charAt(0).toUpperCase() + value.slice(1);
}

export function toIsoDate(date: Date): string {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
}

export function parseIsoDate(value: string): Date {
    const [year, month, day] = value.split('-').map(Number);

    return new Date(year ?? 1970, (month ?? 1) - 1, day ?? 1);
}

export function startOfMonth(date: Date): Date {
    return new Date(date.getFullYear(), date.getMonth(), 1);
}

export function addMonths(date: Date, amount: number): Date {
    return new Date(date.getFullYear(), date.getMonth() + amount, 1);
}

/**
 * Construye la cuadrícula del mes con la semana iniciando en lunes.
 *
 * @returns 35 o 42 celdas; `null` representa un espacio vacío.
 */
export function buildCalendarDays(cursor: Date): Array<Date | null> {
    const firstDay = startOfMonth(cursor);
    const leadingBlanks = (firstDay.getDay() + 6) % 7;
    const daysInMonth = new Date(
        cursor.getFullYear(),
        cursor.getMonth() + 1,
        0,
    ).getDate();

    const cells: Array<Date | null> = Array.from(
        { length: leadingBlanks },
        () => null,
    );

    for (let day = 1; day <= daysInMonth; day++) {
        cells.push(new Date(cursor.getFullYear(), cursor.getMonth(), day));
    }

    while (cells.length % 7 !== 0) {
        cells.push(null);
    }

    return cells;
}

export function formatMonthTitle(date: Date): string {
    return capitalize(`${MONTH_NAMES[date.getMonth()]} ${date.getFullYear()}`);
}

export function formatLongDate(iso: string): string {
    if (!iso) {
        return '';
    }

    const date = parseIsoDate(iso);

    return `${WEEKDAY_LONG[date.getDay()]} ${date.getDate()} de ${
        MONTH_NAMES[date.getMonth()]
    } de ${date.getFullYear()}`;
}

export function formatShortDate(iso: string): string {
    if (!iso) {
        return '';
    }

    const date = parseIsoDate(iso);

    return `${date.getDate()} ${MONTH_NAMES[date.getMonth()].slice(0, 3)} ${date.getFullYear()}`;
}

export function formatPhone(phone: string): string {
    const digits = phone.replace(/\D/g, '');

    if (digits.length === 10) {
        return `${digits.slice(0, 2)} ${digits.slice(2, 6)} ${digits.slice(6)}`;
    }

    return phone;
}

export function slotKey(fecha: string, hora: string): string {
    return `${fecha}T${hora}`;
}

export function statusLabel(status: AppointmentStatus): string {
    return STATUS_LABELS[status];
}

export function patientTypeLabel(type: PatientType): string {
    return PATIENT_TYPE_LABELS[type];
}
