<script setup lang="ts">
import { computed } from 'vue';
import {
    Baby,
    CalendarDays,
    Clock,
    Phone,
    Stethoscope,
    UserRound,
} from '@lucide/vue';
import { formatLongDate, formatPhone } from '@/lib/appointments';
import type { PatientType } from '@/types';

const props = defineProps<{
    servicio: string;
    fecha: string;
    hora: string;
    tipoPaciente: PatientType;
    pacienteNombre: string;
    pacienteTelefono: string;
}>();

const fechaLabel = computed(() =>
    props.fecha ? formatLongDate(props.fecha) : 'Por elegir',
);

const pacienteLabel = computed(
    () => props.pacienteNombre.trim() || 'Por completar',
);

const telefonoLabel = computed(() =>
    props.pacienteTelefono.trim()
        ? formatPhone(props.pacienteTelefono)
        : 'Por completar',
);

const tipoLabel = computed(() =>
    props.tipoPaciente === 'nino' ? 'Niño / niña' : 'Adulto',
);

const completo = computed(
    () =>
        Boolean(props.fecha) &&
        Boolean(props.hora) &&
        Boolean(props.servicio) &&
        Boolean(props.pacienteNombre.trim()) &&
        Boolean(props.pacienteTelefono.trim()),
);
</script>

<template>
    <div class="rounded-2xl border border-luda-sky/40 bg-white/90 p-5">
        <div class="flex items-center justify-between gap-3">
            <p class="text-sm font-semibold text-luda-navy">
                Resumen de tu cita
            </p>
            <span
                :class="[
                    'rounded-full px-2.5 py-1 text-[10px] font-semibold tracking-[0.16em] uppercase',
                    completo
                        ? 'bg-emerald-50 text-emerald-700'
                        : 'bg-luda-sky/20 text-luda-blue',
                ]"
            >
                {{ completo ? 'Listo' : 'En vivo' }}
            </span>
        </div>

        <dl class="mt-4 space-y-3 text-sm">
            <div class="flex items-start gap-3">
                <Stethoscope class="mt-0.5 size-4 shrink-0 text-luda-blue" />
                <div class="min-w-0 flex-1">
                    <dt class="text-[11px] text-luda-ink/45">Servicio</dt>
                    <dd class="truncate font-medium text-luda-navy">
                        {{ servicio || 'Por elegir' }}
                    </dd>
                </div>
            </div>

            <div class="flex items-start gap-3">
                <CalendarDays class="mt-0.5 size-4 shrink-0 text-luda-blue" />
                <div class="min-w-0 flex-1">
                    <dt class="text-[11px] text-luda-ink/45">Fecha</dt>
                    <dd class="font-medium capitalize text-luda-navy">
                        {{ fechaLabel }}
                    </dd>
                </div>
            </div>

            <div class="flex items-start gap-3">
                <Clock class="mt-0.5 size-4 shrink-0 text-luda-blue" />
                <div class="min-w-0 flex-1">
                    <dt class="text-[11px] text-luda-ink/45">Hora</dt>
                    <dd class="font-medium text-luda-navy">
                        {{ hora ? `${hora} hrs` : 'Por elegir' }}
                    </dd>
                </div>
            </div>

            <div class="flex items-start gap-3">
                <UserRound class="mt-0.5 size-4 shrink-0 text-luda-blue" />
                <div class="min-w-0 flex-1">
                    <dt class="text-[11px] text-luda-ink/45">Paciente</dt>
                    <dd class="truncate font-medium text-luda-navy">
                        {{ pacienteLabel }}
                    </dd>
                </div>
            </div>

            <div class="flex items-start gap-3">
                <Phone class="mt-0.5 size-4 shrink-0 text-luda-blue" />
                <div class="min-w-0 flex-1">
                    <dt class="text-[11px] text-luda-ink/45">Teléfono</dt>
                    <dd class="font-medium text-luda-navy">
                        {{ telefonoLabel }}
                    </dd>
                </div>
            </div>

            <div class="flex items-start gap-3">
                <Baby class="mt-0.5 size-4 shrink-0 text-luda-blue" />
                <div class="min-w-0 flex-1">
                    <dt class="text-[11px] text-luda-ink/45">Tipo de cita</dt>
                    <dd class="font-medium text-luda-navy">{{ tipoLabel }}</dd>
                </div>
            </div>
        </dl>

        <p
            class="mt-4 rounded-xl bg-luda-mist px-3 py-2 text-[11px] leading-relaxed text-luda-ink/55"
        >
            Tu cita queda como <strong class="text-luda-navy">pendiente</strong>
            hasta que la confirmes por WhatsApp con tu folio.
        </p>
    </div>
</template>
