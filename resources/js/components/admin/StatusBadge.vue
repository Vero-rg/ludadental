<script setup lang="ts">
import { computed } from 'vue';
import type { AppointmentStatus } from '@/types';

const props = defineProps<{
    estado: AppointmentStatus;
}>();

const styles: Record<
    AppointmentStatus,
    { label: string; badge: string; dot: string }
> = {
    pendiente: {
        label: 'Pendiente',
        badge: 'bg-amber-50 text-amber-700 ring-amber-200/70',
        dot: 'bg-amber-500',
    },
    confirmada: {
        label: 'Confirmada',
        badge: 'bg-emerald-50 text-emerald-700 ring-emerald-200/70',
        dot: 'bg-emerald-500',
    },
    cancelada: {
        label: 'Cancelada',
        badge: 'bg-rose-50 text-rose-700 ring-rose-200/70',
        dot: 'bg-rose-500',
    },
    completada: {
        label: 'Completada',
        badge: 'bg-slate-100 text-slate-600 ring-slate-200',
        dot: 'bg-slate-500',
    },
};

const current = computed(() => styles[props.estado] ?? styles.pendiente);
</script>

<template>
    <span
        :class="[
            'inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold ring-1 ring-inset',
            current.badge,
        ]"
    >
        <span :class="['size-1.5 rounded-full', current.dot]"></span>
        {{ current.label }}
    </span>
</template>
