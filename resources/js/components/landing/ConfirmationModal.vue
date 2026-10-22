<script setup lang="ts">
import { computed } from 'vue';
import { Check, MessageCircle, TriangleAlert } from '@lucide/vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogTitle,
} from '@/components/ui/dialog';
import { formatLongDate, formatPhone } from '@/lib/appointments';
import { buildPatientMessage, buildWhatsAppUrl } from '@/lib/whatsapp';
import type { AppointmentRecord, ClinicInfo } from '@/types';

const props = defineProps<{
    appointment: AppointmentRecord | null;
    clinic: ClinicInfo;
}>();

const emit = defineEmits<{
    close: [];
}>();

const open = computed({
    get: () => props.appointment !== null,
    set: (value: boolean) => {
        if (!value) {
            emit('close');
        }
    },
});

const whatsappUrl = computed(() => {
    if (!props.appointment) {
        return '';
    }

    return buildWhatsAppUrl(
        props.clinic.whatsapp,
        buildPatientMessage(props.appointment, props.clinic),
    );
});
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent
            class="overflow-hidden border-0 bg-white p-0 sm:max-w-md"
            :show-close-button="false"
        >
            <div
                v-if="appointment"
                class="relative overflow-hidden bg-linear-to-br from-luda-navy via-[#03244a] to-luda-blue px-6 pt-9 pb-8 text-center text-white"
            >
                <div
                    class="absolute -top-16 -right-10 size-40 rounded-full bg-luda-sky/20 blur-2xl"
                    aria-hidden="true"
                ></div>
                <span
                    class="relative mx-auto flex size-14 items-center justify-center rounded-full bg-white/10 ring-1 ring-white/20"
                >
                    <Check class="size-7 text-luda-sky" />
                </span>
                <DialogTitle class="relative mt-4 text-xl font-semibold">
                    ¡Tu cita está registrada!
                </DialogTitle>
                <DialogDescription class="relative mt-1 text-sm text-white/65">
                    Guarda tu folio, lo necesitarás para confirmar.
                </DialogDescription>
                <p
                    class="relative mt-4 font-mono text-2xl font-bold tracking-[0.18em] text-luda-sky"
                >
                    {{ appointment.codigo_folio }}
                </p>
            </div>

            <div v-if="appointment" class="px-6 py-6">
                <dl class="space-y-3 text-sm">
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-luda-ink/50">Paciente</dt>
                        <dd class="text-right font-medium text-luda-navy">
                            {{ appointment.paciente_nombre }}
                        </dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-luda-ink/50">Servicio</dt>
                        <dd class="text-right font-medium text-luda-navy">
                            {{ appointment.servicio }}
                        </dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-luda-ink/50">Fecha</dt>
                        <dd
                            class="text-right font-medium text-luda-navy capitalize"
                        >
                            {{ formatLongDate(appointment.fecha) }}
                        </dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-luda-ink/50">Hora</dt>
                        <dd class="text-right font-medium text-luda-navy">
                            {{ appointment.hora }} hrs
                        </dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-luda-ink/50">Teléfono</dt>
                        <dd class="text-right font-medium text-luda-navy">
                            {{ formatPhone(appointment.paciente_telefono) }}
                        </dd>
                    </div>
                </dl>

                <p
                    class="mt-5 flex gap-2 rounded-xl bg-amber-50 px-4 py-3 text-xs leading-relaxed text-amber-800 ring-1 ring-amber-200/60"
                >
                    <TriangleAlert class="mt-0.5 size-4 shrink-0" />
                    <span>
                        Envía tu folio por WhatsApp para confirmar la cita. El
                        horario queda apartado como
                        <strong>pendiente</strong> mientras tanto.
                    </span>
                </p>

                <a
                    :href="whatsappUrl"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="mt-5 flex w-full items-center justify-center gap-2 rounded-xl bg-[#25D366] px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-500/20 transition hover:bg-[#1ebe5b]"
                >
                    <MessageCircle class="size-4" />
                    Confirmar por WhatsApp
                </a>

                <button
                    type="button"
                    class="mt-3 w-full rounded-xl border border-luda-navy/10 px-5 py-3 text-sm font-semibold text-luda-navy transition hover:bg-luda-mist"
                    @click="emit('close')"
                >
                    Cerrar
                </button>
            </div>
        </DialogContent>
    </Dialog>
</template>
