<script setup lang="ts">
import { computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { CalendarClock, LoaderCircle, MessageCircle } from '@lucide/vue';
import AppointmentCalendar from '@/components/landing/AppointmentCalendar.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogDescription,
    DialogFooter,
    DialogScrollContent,
    DialogTitle,
} from '@/components/ui/dialog';
import { formatLongDate, formatPhone } from '@/lib/appointments';
import { buildReminderMessage, buildWhatsAppUrl } from '@/lib/whatsapp';
import type {
    AppointmentRecord,
    ClinicInfo,
    OccupiedSlot,
    WeeklySlots,
} from '@/types';

const props = withDefaults(
    defineProps<{
        appointment: AppointmentRecord | null;
        weeklySlots: WeeklySlots;
        occupiedSlots: OccupiedSlot[];
        clinic: ClinicInfo;
        today: string;
        monthsAhead?: number;
    }>(),
    {
        monthsAhead: 6,
    },
);

const emit = defineEmits<{
    close: [];
}>();

const form = useForm({
    fecha: '',
    hora: '',
});

const open = computed({
    get: () => props.appointment !== null,
    set: (value: boolean) => {
        if (!value) {
            emit('close');
        }
    },
});

const otherOccupied = computed(() => {
    if (!props.appointment) {
        return props.occupiedSlots;
    }

    return props.occupiedSlots.filter(
        (slot) => slot.id !== props.appointment?.id,
    );
});

const whatsappUrl = computed(() => {
    if (!props.appointment) {
        return '';
    }

    return buildWhatsAppUrl(
        props.appointment.paciente_telefono,
        buildReminderMessage(
            {
                ...props.appointment,
                fecha: form.fecha || props.appointment.fecha,
                hora: form.hora || props.appointment.hora,
            },
            props.clinic,
        ),
    );
});

watch(
    () => props.appointment,
    (appointment) => {
        if (!appointment) {
            return;
        }

        form.clearErrors();
        form.fecha = appointment.fecha;
        form.hora = appointment.hora;
    },
    { immediate: true },
);

function submit(): void {
    if (!props.appointment) {
        return;
    }

    form.patch(`/admin/citas/${props.appointment.id}/reagendar`, {
        preserveScroll: true,
        onSuccess: () => emit('close'),
    });
}
</script>

<template>
    <Dialog v-model:open="open">
        <DialogScrollContent class="gap-0 border-0 bg-luda-mist p-0 sm:max-w-xl">
            <div
                v-if="appointment"
                class="flex items-start justify-between gap-4 border-b border-luda-navy/5 bg-white px-6 pt-6 pb-5"
            >
                <div>
                    <DialogTitle
                        class="flex items-center gap-2 text-lg font-semibold text-luda-navy"
                    >
                        <CalendarClock class="size-5 text-luda-blue" />
                        Reagendar cita
                    </DialogTitle>
                    <DialogDescription class="mt-1 text-sm text-luda-ink/55">
                        {{ appointment.paciente_nombre }} ·
                        {{ formatPhone(appointment.paciente_telefono) }}
                    </DialogDescription>
                    <p class="mt-2 text-xs text-luda-ink/45">
                        <span class="font-mono font-semibold text-luda-blue">
                            {{ appointment.codigo_folio }}
                        </span>
                        · Actualmente
                        <span class="capitalize">
                            {{ formatLongDate(appointment.fecha) }}
                        </span>
                        a las {{ appointment.hora }} hrs
                    </p>
                </div>
            </div>

            <div v-if="appointment" class="px-5 py-5 sm:px-6">
                <AppointmentCalendar
                    v-model:date="form.fecha"
                    v-model:time="form.hora"
                    :weekly-slots="weeklySlots"
                    :occupied-slots="otherOccupied"
                    :today="today"
                    :months-ahead="monthsAhead"
                    :error="form.errors.fecha || form.errors.hora"
                />

                <div
                    class="mt-4 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-luda-navy/5 bg-white px-4 py-3"
                >
                    <div class="text-xs text-luda-ink/55">
                        Nueva fecha:
                        <span class="font-semibold text-luda-navy">
                            {{
                                form.fecha
                                    ? formatLongDate(form.fecha)
                                    : 'Por elegir'
                            }}
                        </span>
                        <span v-if="form.hora">
                            · {{ form.hora }} hrs
                        </span>
                    </div>
                    <a
                        v-if="form.fecha && form.hora"
                        :href="whatsappUrl"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-100"
                    >
                        <MessageCircle class="size-3.5" />
                        Notificar por WhatsApp
                    </a>
                </div>
            </div>

            <DialogFooter
                class="flex-col-reverse gap-2 rounded-b-lg border-t border-luda-navy/5 bg-white px-5 py-4 sm:flex-row sm:justify-end sm:px-6"
            >
                <Button
                    type="button"
                    variant="outline"
                    class="border-luda-navy/10 text-luda-navy hover:bg-luda-mist"
                    @click="emit('close')"
                >
                    Cancelar
                </Button>
                <Button
                    type="button"
                    class="bg-luda-navy text-white hover:bg-luda-blue disabled:opacity-50"
                    :disabled="form.processing || !form.fecha || !form.hora"
                    @click="submit"
                >
                    <LoaderCircle
                        v-if="form.processing"
                        class="size-4 animate-spin"
                    />
                    <CalendarClock v-else class="size-4" />
                    Guardar cambios
                </Button>
            </DialogFooter>
        </DialogScrollContent>
    </Dialog>
</template>
