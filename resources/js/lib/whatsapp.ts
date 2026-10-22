import type { AppointmentRecord, ClinicInfo } from '@/types';
import { formatLongDate, formatPhone } from './appointments';

const CLINIC_NAME = 'LuDa Dental';

export function normalizeWhatsAppNumber(phone: string): string {
    const digits = phone.replace(/\D/g, '');

    if (digits.length === 10) {
        return `52${digits}`;
    }

    if (digits.length === 13 && digits.startsWith('521')) {
        return digits;
    }

    return digits;
}

export function buildWhatsAppUrl(phone: string, message: string): string {
    return `https://wa.me/${normalizeWhatsAppNumber(phone)}?text=${encodeURIComponent(message)}`;
}

/**
 * Mensaje del paciente hacia la clínica al terminar de agendar.
 */
export function buildPatientMessage(
    appointment: AppointmentRecord,
    clinic: ClinicInfo,
): string {
    return [
        `¡Hola ${clinic.name || CLINIC_NAME}! 🦷`,
        'Acabo de agendar mi cita y quiero confirmarla:',
        '',
        `• Folio: ${appointment.codigo_folio}`,
        `• Servicio: ${appointment.servicio}`,
        `• Fecha: ${formatLongDate(appointment.fecha)}`,
        `• Hora: ${appointment.hora} hrs`,
        `• Paciente: ${appointment.paciente_nombre}`,
        `• Teléfono: ${formatPhone(appointment.paciente_telefono)}`,
        ...(appointment.tipo_paciente === 'nino'
            ? ['• Cita para: niño / niña']
            : []),
        '',
        '¡Gracias!',
    ].join('\n');
}

/**
 * Recordatorio o confirmación que la clínica envía al paciente.
 */
export function buildReminderMessage(
    appointment: AppointmentRecord,
    clinic: ClinicInfo,
): string {
    return [
        `Hola ${appointment.paciente_nombre}, te saludamos de ${
            clinic.name || CLINIC_NAME
        } 🦷`,
        '',
        'Te compartimos los datos de tu cita:',
        `• Folio: ${appointment.codigo_folio}`,
        `• Servicio: ${appointment.servicio}`,
        `• Fecha: ${formatLongDate(appointment.fecha)}`,
        `• Hora: ${appointment.hora} hrs`,
        '',
        `📍 ${clinic.address.line1}, ${clinic.address.line2}`,
        'Si necesitas reagendar o tienes alguna duda, respóndenos por este medio.',
    ].join('\n');
}
