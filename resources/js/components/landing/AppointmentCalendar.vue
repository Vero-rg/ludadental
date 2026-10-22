<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import {
    CalendarDays,
    ChevronLeft,
    ChevronRight,
    Clock,
    Info,
} from '@lucide/vue';
import {
    addMonths,
    buildCalendarDays,
    capitalize,
    formatLongDate,
    formatMonthTitle,
    formatShortDate,
    parseIsoDate,
    slotKey,
    startOfMonth,
    toIsoDate,
    WEEKDAY_SHORT,
} from '@/lib/appointments';
import type { OccupiedSlot, WeeklySlots } from '@/types';

const props = withDefaults(
    defineProps<{
        weeklySlots: WeeklySlots;
        occupiedSlots: OccupiedSlot[];
        today: string;
        monthsAhead?: number;
        error?: string;
    }>(),
    {
        monthsAhead: 3,
        error: '',
    },
);

const selectedDate = defineModel<string>('date', { default: '' });
const selectedTime = defineModel<string>('time', { default: '' });

const todayIso = computed(() => props.today || toIsoDate(new Date()));

const cursor = ref(
    selectedDate.value
        ? startOfMonth(parseIsoDate(selectedDate.value))
        : startOfMonth(parseIsoDate(todayIso.value)),
);

const currentMonthIso = computed(() => `${todayIso.value.slice(0, 7)}-01`);
const maxMonth = computed(() =>
    addMonths(startOfMonth(parseIsoDate(todayIso.value)), props.monthsAhead),
);

const canGoBack = computed(() => toIsoDate(cursor.value) > currentMonthIso.value);
const canGoForward = computed(
    () => cursor.value.getTime() < maxMonth.value.getTime(),
);

const days = computed(() => buildCalendarDays(cursor.value));

const takenKeys = computed(
    () =>
        new Set(
            props.occupiedSlots.map((slot) => slotKey(slot.fecha, slot.hora)),
        ),
);

const selectedWeekday = computed(() =>
    selectedDate.value ? parseIsoDate(selectedDate.value).getDay() : -1,
);

const availableSlots = computed(
    () => props.weeklySlots[selectedWeekday.value] ?? [],
);

const selectedDateLabel = computed(() =>
    selectedDate.value ? capitalize(formatShortDate(selectedDate.value)) : '',
);

watch(selectedDate, (value) => {
    if (!value) {
        return;
    }

    const parsed = parseIsoDate(value);

    if (
        parsed.getMonth() !== cursor.value.getMonth() ||
        parsed.getFullYear() !== cursor.value.getFullYear()
    ) {
        cursor.value = startOfMonth(parsed);
    }
});

function slotsForDay(day: Date): string[] {
    return props.weeklySlots[day.getDay()] ?? [];
}

function isPastSlot(iso: string, slot: string): boolean {
    if (iso !== todayIso.value) {
        return false;
    }

    const now = new Date();
    const [hours, minutes] = slot.split(':').map(Number);

    return (
        (hours ?? 0) * 60 + (minutes ?? 0) <=
        now.getHours() * 60 + now.getMinutes()
    );
}

function isSlotTaken(slot: string): boolean {
    return takenKeys.value.has(slotKey(selectedDate.value, slot));
}

function isSlotUnavailable(slot: string): boolean {
    return isSlotTaken(slot) || isPastSlot(selectedDate.value, slot);
}

function dayStatus(day: Date): 'past' | 'closed' | 'full' | 'open' {
    const iso = toIsoDate(day);

    if (iso < todayIso.value) {
        return 'past';
    }

    const slots = slotsForDay(day);

    if (slots.length === 0) {
        return 'closed';
    }

    const bookableSlots = slots.filter((slot) => !isPastSlot(iso, slot));

    if (
        bookableSlots.length === 0 ||
        bookableSlots.every((slot) => takenKeys.value.has(slotKey(iso, slot)))
    ) {
        return 'full';
    }

    return 'open';
}

function isDayDisabled(day: Date): boolean {
    return dayStatus(day) !== 'open';
}

function selectDay(day: Date): void {
    if (isDayDisabled(day)) {
        return;
    }

    const iso = toIsoDate(day);

    if (selectedDate.value !== iso) {
        selectedTime.value = '';
    }

    selectedDate.value = iso;
}

function selectSlot(slot: string): void {
    if (isSlotUnavailable(slot)) {
        return;
    }

    selectedTime.value = selectedTime.value === slot ? '' : slot;
}

function dayButtonClass(day: Date): string {
    const iso = toIsoDate(day);

    if (selectedDate.value === iso) {
        return 'bg-luda-navy text-white shadow-lg shadow-luda-navy/25';
    }

    if (dayStatus(day) !== 'open') {
        return 'cursor-not-allowed text-luda-ink/20';
    }

    if (iso === todayIso.value) {
        return 'bg-luda-sky/20 font-bold text-luda-navy ring-1 ring-luda-sky/70 hover:bg-luda-sky/30';
    }

    return 'text-luda-ink/75 hover:bg-luda-mist hover:text-luda-navy';
}

function slotButtonClass(slot: string): string {
    if (selectedTime.value === slot) {
        return 'border-luda-navy bg-luda-navy text-white shadow-md shadow-luda-navy/20';
    }

    if (isSlotUnavailable(slot)) {
        return 'cursor-not-allowed border-transparent bg-luda-mist text-luda-ink/25 line-through';
    }

    return 'border-luda-navy/10 bg-white text-luda-ink/70 hover:border-luda-sky hover:bg-luda-mist hover:text-luda-navy';
}

function changeMonth(delta: number): void {
    const next = addMonths(cursor.value, delta);

    if (toIsoDate(next) < currentMonthIso.value) {
        return;
    }

    if (next.getTime() > maxMonth.value.getTime()) {
        return;
    }

    cursor.value = next;
}
</script>

<template>
    <div
        class="rounded-3xl border border-luda-navy/5 bg-white p-5 shadow-[0_24px_70px_-50px_rgba(0,19,48,0.55)] sm:p-6"
    >
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span
                    class="flex size-10 shrink-0 items-center justify-center rounded-2xl bg-luda-mist text-luda-blue"
                >
                    <CalendarDays class="size-5" />
                </span>
                <div>
                    <p
                        class="text-[10px] font-semibold tracking-[0.22em] text-luda-blue uppercase"
                    >
                        Calendario
                    </p>
                    <p class="text-base font-semibold text-luda-navy">
                        {{ formatMonthTitle(cursor) }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-1">
                <button
                    type="button"
                    class="flex size-9 items-center justify-center rounded-full border border-luda-navy/10 text-luda-navy transition hover:bg-luda-mist disabled:cursor-not-allowed disabled:opacity-30 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-luda-blue/30"
                    :disabled="!canGoBack"
                    aria-label="Mes anterior"
                    @click="changeMonth(-1)"
                >
                    <ChevronLeft class="size-4" />
                </button>
                <button
                    type="button"
                    class="flex size-9 items-center justify-center rounded-full border border-luda-navy/10 text-luda-navy transition hover:bg-luda-mist disabled:cursor-not-allowed disabled:opacity-30 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-luda-blue/30"
                    :disabled="!canGoForward"
                    aria-label="Mes siguiente"
                    @click="changeMonth(1)"
                >
                    <ChevronRight class="size-4" />
                </button>
            </div>
        </div>

        <div
            class="mt-6 grid grid-cols-7 gap-1 text-center text-[10px] font-semibold tracking-wider text-luda-ink/40 uppercase"
        >
            <span v-for="label in WEEKDAY_SHORT" :key="label" class="py-1">
                {{ label }}
            </span>
        </div>

        <div class="mt-1 grid grid-cols-7 gap-1 sm:gap-1.5">
            <template v-for="(day, index) in days" :key="index">
                <div v-if="!day"></div>
                <button
                    v-else
                    type="button"
                    :class="[
                        'flex h-10 items-center justify-center rounded-xl text-sm font-medium transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-luda-blue/40 sm:h-11',
                        dayButtonClass(day),
                    ]"
                    :disabled="isDayDisabled(day)"
                    :aria-pressed="selectedDate === toIsoDate(day)"
                    :aria-label="toIsoDate(day)"
                    @click="selectDay(day)"
                >
                    {{ day.getDate() }}
                </button>
            </template>
        </div>

        <div
            class="mt-5 flex flex-wrap items-center gap-x-4 gap-y-2 text-[11px] text-luda-ink/50"
        >
            <span class="flex items-center gap-1.5">
                <span class="size-2 rounded-full bg-luda-blue"></span>
                Disponible
            </span>
            <span class="flex items-center gap-1.5">
                <span class="size-2 rounded-full bg-luda-sky"></span>
                Hoy
            </span>
            <span class="flex items-center gap-1.5">
                <span class="size-2 rounded-full bg-luda-ink/15"></span>
                Sin lugar
            </span>
        </div>

        <div class="mt-6 border-t border-luda-navy/5 pt-5">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <p class="flex items-center gap-2 text-sm font-semibold text-luda-navy">
                    <Clock class="size-4 text-luda-blue" />
                    Horarios disponibles
                </p>
                <p
                    v-if="selectedDate"
                    class="text-xs font-medium text-luda-ink/55"
                >
                    {{ selectedDateLabel }}
                </p>
            </div>

            <div
                v-if="!selectedDate"
                class="mt-4 flex items-center gap-2 rounded-xl bg-luda-mist px-4 py-3 text-xs text-luda-ink/60"
            >
                <Info class="size-4 shrink-0 text-luda-blue" />
                Primero elige un día disponible en el calendario.
            </div>

            <div
                v-else-if="availableSlots.length === 0"
                class="mt-4 flex items-center gap-2 rounded-xl bg-luda-mist px-4 py-3 text-xs text-luda-ink/60"
            >
                <Info class="size-4 shrink-0 text-luda-blue" />
                Este día no hay atención. Te esperamos de lunes a sábado.
            </div>

            <div v-else class="mt-4 grid grid-cols-3 gap-2 sm:grid-cols-4">
                <button
                    v-for="slot in availableSlots"
                    :key="slot"
                    type="button"
                    :class="[
                        'rounded-xl border px-2 py-2.5 text-center text-sm font-semibold transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-luda-blue/40',
                        slotButtonClass(slot),
                    ]"
                    :disabled="isSlotUnavailable(slot)"
                    :aria-pressed="selectedTime === slot"
                    @click="selectSlot(slot)"
                >
                    {{ slot }}
                    <span class="block text-[10px] font-normal opacity-60">
                        hrs
                    </span>
                </button>
            </div>

            <p v-if="error" class="mt-3 text-xs font-medium text-red-600">
                {{ error }}
            </p>

            <p class="mt-4 text-[11px] leading-relaxed text-luda-ink/45">
                Horario de Zapopan: lunes a viernes de 9:00 a 17:00 y sábados
                de 9:00 a 13:00. Domingos cerrado.
                <span v-if="selectedDate">
                    La fecha completa es {{ formatLongDate(selectedDate) }}.
                </span>
            </p>
        </div>
    </div>
</template>
