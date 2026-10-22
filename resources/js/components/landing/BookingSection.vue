<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import {
    CalendarCheck,
    ChevronDown,
    LoaderCircle,
    ShieldCheck,
    TriangleAlert,
    UserRound,
} from '@lucide/vue';
import AppointmentCalendar from './AppointmentCalendar.vue';
import BookingSummary from './BookingSummary.vue';
import ConfirmationModal from './ConfirmationModal.vue';
import InputError from '@/components/InputError.vue';
import type {
    AppointmentRecord,
    ClinicInfo,
    OccupiedSlot,
    PatientType,
    WeeklySlots,
} from '@/types';

const props = defineProps<{
    services: string[];
    weeklySlots: WeeklySlots;
    occupiedSlots: OccupiedSlot[];
    clinic: ClinicInfo;
    today: string;
    preselectedService: string;
    preselectedType: PatientType;
}>();

const localOccupied = ref<OccupiedSlot[]>([...props.occupiedSlots]);
const confirmedAppointment = ref<AppointmentRecord | null>(null);

const form = useForm({
    paciente_nombre: '',
    paciente_telefono: '',
    paciente_email: '',
    servicio: props.services[0] ?? '',
    fecha: '',
    hora: '',
    tipo_paciente: 'adulto' as PatientType,
    notas: '',
});

const canSubmit = computed(
    () =>
        Boolean(form.fecha) &&
        Boolean(form.hora) &&
        Boolean(form.paciente_nombre.trim()) &&
        Boolean(form.paciente_telefono.trim()) &&
        !form.processing,
);

watch(
    () => props.preselectedService,
    (service) => {
        if (service) {
            form.servicio = service;
        }
    },
);

watch(
    () => props.preselectedType,
    (type) => {
        if (type) {
            form.tipo_paciente = type;
        }
    },
);

function normalizePhone(): void {
    const digits = form.paciente_telefono
        .replace(/\D/g, '')
        .slice(-10);

    form.paciente_telefono = digits;
}

function submit(): void {
    form.post('/citas', {
        preserveScroll: true,
        onSuccess: (page) => {
            const flash = page.flash as {
                appointment?: AppointmentRecord;
            };

            if (flash.appointment) {
                confirmedAppointment.value = flash.appointment;
                localOccupied.value = [
                    ...localOccupied.value,
                    {
                        fecha: flash.appointment.fecha,
                        hora: flash.appointment.hora,
                        id: flash.appointment.id,
                    },
                ];
            }

            form.reset(
                'paciente_nombre',
                'paciente_telefono',
                'paciente_email',
                'fecha',
                'hora',
                'notas',
            );
        },
    });
}

const fieldClass =
    'w-full rounded-xl border border-luda-navy/10 bg-white px-3.5 py-2.5 text-sm text-luda-ink shadow-sm outline-none transition placeholder:text-luda-ink/30 focus:border-luda-sky focus:ring-2 focus:ring-luda-sky/40';
</script>

<template>
    <section id="agendar" class="relative bg-white py-20 sm:py-24">
        <div
            class="absolute inset-x-0 top-0 h-px bg-linear-to-r from-transparent via-luda-sky/70 to-transparent"
            aria-hidden="true"
        ></div>

        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <div class="mx-auto max-w-2xl text-center">
                <p
                    class="text-xs font-semibold tracking-[0.28em] text-luda-blue uppercase"
                >
                    Agenda en línea
                </p>
                <h2
                    class="mt-3 text-3xl font-semibold tracking-tight text-luda-navy sm:text-4xl"
                >
                    Reserva tu cita en menos de un minuto
                </h2>
                <p class="mt-4 text-base leading-relaxed text-luda-ink/65">
                    Elige el día y la hora que mejor te acomoden. Al terminar
                    recibirás un folio y podrás confirmar tu cita por WhatsApp.
                </p>
            </div>

            <div
                class="mt-12 grid gap-6 lg:grid-cols-[1.08fr_0.92fr] lg:items-start"
            >
                <AppointmentCalendar
                    v-model:date="form.fecha"
                    v-model:time="form.hora"
                    :weekly-slots="weeklySlots"
                    :occupied-slots="localOccupied"
                    :today="today"
                    :error="form.errors.fecha || form.errors.hora"
                />

                <div
                    class="rounded-3xl border border-luda-navy/5 bg-luda-mist/70 p-5 shadow-[0_24px_70px_-52px_rgba(0,19,48,0.6)] sm:p-6"
                >
                    <form class="space-y-4" novalidate @submit.prevent="submit">
                        <div
                            v-if="form.hasErrors"
                            class="flex items-start gap-2 rounded-xl bg-red-50 px-4 py-3 text-xs text-red-700 ring-1 ring-red-200"
                        >
                            <TriangleAlert class="mt-0.5 size-4 shrink-0" />
                            Revisa los campos marcados para continuar.
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <label
                                    for="paciente_nombre"
                                    class="text-xs font-semibold text-luda-navy"
                                >
                                    Nombre del paciente
                                </label>
                                <input
                                    id="paciente_nombre"
                                    v-model="form.paciente_nombre"
                                    type="text"
                                    name="paciente_nombre"
                                    autocomplete="name"
                                    placeholder="Nombre completo"
                                    :class="[
                                        fieldClass,
                                        form.errors.paciente_nombre
                                            ? 'border-red-300 focus:border-red-400 focus:ring-red-200'
                                            : '',
                                    ]"
                                />
                                <InputError
                                    :message="form.errors.paciente_nombre"
                                />
                            </div>

                            <div class="grid gap-2">
                                <label
                                    for="paciente_telefono"
                                    class="text-xs font-semibold text-luda-navy"
                                >
                                    Teléfono
                                </label>
                                <input
                                    id="paciente_telefono"
                                    v-model="form.paciente_telefono"
                                    type="tel"
                                    name="paciente_telefono"
                                    inputmode="tel"
                                    autocomplete="tel"
                                    maxlength="16"
                                    placeholder="33 1234 5678"
                                    :class="[
                                        fieldClass,
                                        form.errors.paciente_telefono
                                            ? 'border-red-300 focus:border-red-400 focus:ring-red-200'
                                            : '',
                                    ]"
                                    @blur="normalizePhone"
                                />
                                <InputError
                                    :message="form.errors.paciente_telefono"
                                />
                            </div>
                        </div>

                        <div class="grid gap-2">
                            <label
                                for="paciente_email"
                                class="text-xs font-semibold text-luda-navy"
                            >
                                Correo electrónico
                                <span class="font-normal text-luda-ink/40">
                                    (opcional)
                                </span>
                            </label>
                            <input
                                id="paciente_email"
                                v-model="form.paciente_email"
                                type="email"
                                name="paciente_email"
                                autocomplete="email"
                                placeholder="tucorreo@ejemplo.com"
                                :class="[
                                    fieldClass,
                                    form.errors.paciente_email
                                        ? 'border-red-300 focus:border-red-400 focus:ring-red-200'
                                        : '',
                                ]"
                            />
                            <InputError :message="form.errors.paciente_email" />
                        </div>

                        <div class="grid gap-2">
                            <label
                                for="servicio"
                                class="text-xs font-semibold text-luda-navy"
                            >
                                Servicio
                            </label>
                            <div class="relative">
                                <select
                                    id="servicio"
                                    v-model="form.servicio"
                                    name="servicio"
                                    :class="[
                                        fieldClass,
                                        'appearance-none pr-10',
                                        form.errors.servicio
                                            ? 'border-red-300 focus:border-red-400 focus:ring-red-200'
                                            : '',
                                    ]"
                                >
                                    <option
                                        v-for="service in services"
                                        :key="service"
                                        :value="service"
                                    >
                                        {{ service }}
                                    </option>
                                </select>
                                <ChevronDown
                                    class="pointer-events-none absolute top-1/2 right-3 size-4 -translate-y-1/2 text-luda-blue"
                                />
                            </div>
                            <InputError :message="form.errors.servicio" />
                        </div>

                        <div class="grid gap-2">
                            <span class="text-xs font-semibold text-luda-navy">
                                Tipo de paciente
                            </span>
                            <div
                                class="grid grid-cols-2 gap-2"
                                role="radiogroup"
                                aria-label="Tipo de paciente"
                            >
                                <button
                                    type="button"
                                    role="radio"
                                    :aria-checked="
                                        form.tipo_paciente === 'adulto'
                                    "
                                    :class="[
                                        'flex items-center justify-center gap-2 rounded-xl border px-3 py-2.5 text-sm font-semibold transition',
                                        form.tipo_paciente === 'adulto'
                                            ? 'border-luda-navy bg-luda-navy text-white shadow-md shadow-luda-navy/20'
                                            : 'border-luda-navy/10 bg-white text-luda-ink/60 hover:bg-white/70',
                                    ]"
                                    @click="form.tipo_paciente = 'adulto'"
                                >
                                    <UserRound class="size-4" />
                                    Adulto
                                </button>
                                <button
                                    type="button"
                                    role="radio"
                                    :aria-checked="form.tipo_paciente === 'nino'"
                                    :class="[
                                        'flex items-center justify-center gap-2 rounded-xl border px-3 py-2.5 text-sm font-semibold transition',
                                        form.tipo_paciente === 'nino'
                                            ? 'border-luda-navy bg-luda-navy text-white shadow-md shadow-luda-navy/20'
                                            : 'border-luda-navy/10 bg-white text-luda-ink/60 hover:bg-white/70',
                                    ]"
                                    @click="form.tipo_paciente = 'nino'"
                                >
                                    <UserRound class="size-4" />
                                    Niño / niña
                                </button>
                            </div>
                            <InputError :message="form.errors.tipo_paciente" />
                        </div>

                        <div class="grid gap-2">
                            <label
                                for="notas"
                                class="text-xs font-semibold text-luda-navy"
                            >
                                Notas
                                <span class="font-normal text-luda-ink/40">
                                    (opcional)
                                </span>
                            </label>
                            <textarea
                                id="notas"
                                v-model="form.notas"
                                name="notas"
                                rows="3"
                                maxlength="1000"
                                placeholder="Cuéntanos algo que debamos saber antes de tu visita."
                                :class="[fieldClass, 'resize-none']"
                            ></textarea>
                            <InputError :message="form.errors.notas" />
                        </div>

                        <BookingSummary
                            :servicio="form.servicio"
                            :fecha="form.fecha"
                            :hora="form.hora"
                            :tipo-paciente="form.tipo_paciente"
                            :paciente-nombre="form.paciente_nombre"
                            :paciente-telefono="form.paciente_telefono"
                        />

                        <button
                            type="submit"
                            :disabled="!canSubmit"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-luda-navy px-6 py-3.5 text-sm font-semibold text-white shadow-xl shadow-luda-navy/25 transition hover:bg-luda-blue focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-luda-blue/50 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-45"
                        >
                            <LoaderCircle
                                v-if="form.processing"
                                class="size-4 animate-spin"
                            />
                            <CalendarCheck v-else class="size-4" />
                            {{
                                form.processing
                                    ? 'Registrando tu cita…'
                                    : 'Confirmar mi cita'
                            }}
                        </button>

                        <p
                            v-if="!form.fecha || !form.hora"
                            class="text-center text-[11px] text-luda-ink/45"
                        >
                            Selecciona fecha y hora para continuar.
                        </p>

                        <p
                            class="flex items-center justify-center gap-1.5 text-center text-[11px] text-luda-ink/45"
                        >
                            <ShieldCheck class="size-3.5 text-luda-blue" />
                            Tus datos se usan únicamente para gestionar tu
                            cita.
                        </p>
                    </form>
                </div>
            </div>
        </div>

        <ConfirmationModal
            :appointment="confirmedAppointment"
            :clinic="clinic"
            @close="confirmedAppointment = null"
        />
    </section>
</template>
