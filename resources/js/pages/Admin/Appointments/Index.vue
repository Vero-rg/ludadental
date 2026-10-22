<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    ArrowUpRight,
    Baby,
    BadgeCheck,
    CalendarCheck,
    CalendarClock,
    CalendarDays,
    CircleCheck,
    CircleX,
    Clock3,
    MessageCircle,
    Phone,
    Search,
    UserRound,
} from '@lucide/vue';
import RescheduleDialog from '@/components/admin/RescheduleDialog.vue';
import StatCard from '@/components/admin/StatCard.vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import {
    formatLongDate,
    formatPhone,
    formatShortDate,
    patientTypeLabel,
} from '@/lib/appointments';
import { buildReminderMessage, buildWhatsAppUrl } from '@/lib/whatsapp';
import type {
    AppointmentRecord,
    AppointmentStatus,
    ClinicInfo,
    OccupiedSlot,
    Paginator,
    WeeklySlots,
} from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Agenda de citas',
                href: '/admin/citas',
            },
        ],
    },
});

const props = defineProps<{
    appointments: Paginator<AppointmentRecord>;
    filters: {
        estado: string;
        fecha: string;
        q: string;
    };
    stats: {
        hoy: number;
        pendientes: number;
        semana: number;
    };
    services: string[];
    weeklySlots: WeeklySlots;
    occupiedSlots: OccupiedSlot[];
    clinic: ClinicInfo;
    today: string;
}>();

const search = ref(props.filters.q);
const dateFilter = ref(props.filters.fecha);
const selectedAppointment = ref<AppointmentRecord | null>(null);

const quickFilters = computed(() => [
    { value: 'todas', label: 'Todas' },
    { value: 'pendientes', label: 'Pendientes', count: props.stats.pendientes },
    { value: 'confirmadas', label: 'Confirmadas' },
    { value: 'hoy', label: 'Hoy', count: props.stats.hoy },
]);

const activeFilter = computed(() => props.filters.estado || 'todas');

const hasFilters = computed(
    () =>
        activeFilter.value !== 'todas' ||
        props.filters.fecha !== '' ||
        props.filters.q !== '',
);

const paginationLinks = computed(() =>
    props.appointments.links.map((link) => ({
        ...link,
        label: link.label
            .replace('&laquo;', '«')
            .replace('&raquo;', '»')
            .replace('&amp;', '&'),
    })),
);

let searchTimer: ReturnType<typeof setTimeout> | undefined;

type FilterOverrides = Partial<{
    estado: string;
    fecha: string;
    q: string;
    page: number;
}>;

function applyFilters(overrides: FilterOverrides = {}): void {
    const params: Record<string, string | number> = {
        estado: overrides.estado ?? props.filters.estado,
        fecha: overrides.fecha ?? props.filters.fecha,
        q: overrides.q ?? search.value,
    };

    if (overrides.page) {
        params.page = overrides.page;
    }

    const cleanParams = Object.fromEntries(
        Object.entries(params).filter(
            ([key, value]) =>
                value !== '' && !(key === 'estado' && value === 'todas'),
        ),
    );

    router.get('/admin/citas', cleanParams, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

function onSearchInput(): void {
    if (searchTimer) {
        clearTimeout(searchTimer);
    }

    searchTimer = setTimeout(() => {
        applyFilters({ q: search.value, page: 1 });
    }, 350);
}

function onDateChange(): void {
    applyFilters({ fecha: dateFilter.value, page: 1 });
}

function setQuickFilter(value: string): void {
    dateFilter.value = '';
    applyFilters({ estado: value, fecha: '', page: 1 });
}

function clearFilters(): void {
    search.value = '';
    dateFilter.value = '';
    applyFilters({ estado: 'todas', fecha: '', q: '', page: 1 });
}

function whatsappUrl(appointment: AppointmentRecord): string {
    return buildWhatsAppUrl(
        appointment.paciente_telefono,
        buildReminderMessage(appointment, props.clinic),
    );
}

function updateStatus(
    appointment: AppointmentRecord,
    estado: AppointmentStatus,
): void {
    router.patch(
        `/admin/citas/${appointment.id}/estado`,
        { estado },
        { preserveScroll: true },
    );
}

function cancelAppointment(appointment: AppointmentRecord): void {
    const confirmed = window.confirm(
        `¿Cancelar la cita ${appointment.codigo_folio} de ${appointment.paciente_nombre}?`,
    );

    if (confirmed) {
        updateStatus(appointment, 'cancelada');
    }
}

function openReschedule(appointment: AppointmentRecord): void {
    selectedAppointment.value = appointment;
}
</script>

<template>
    <Head title="Agenda de citas" />

    <div class="flex h-full flex-1 flex-col gap-6 bg-luda-mist p-4 sm:p-6">
        <header
            class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
        >
            <div>
                <p
                    class="text-[11px] font-semibold tracking-[0.22em] text-luda-blue uppercase"
                >
                    Panel administrativo
                </p>
                <h1
                    class="mt-1 text-2xl font-semibold tracking-tight text-luda-navy sm:text-3xl"
                >
                    Agenda de citas
                </h1>
                <p class="mt-1.5 text-sm text-luda-ink/55">
                    Gestiona, confirma y reagenda las citas de LuDa Dental.
                    <span class="hidden capitalize sm:inline">
                        Hoy es {{ formatLongDate(today) }}.
                    </span>
                </p>
            </div>

            <a
                href="/"
                class="inline-flex w-fit items-center gap-2 rounded-full border border-luda-navy/10 bg-white px-4 py-2 text-sm font-semibold text-luda-navy shadow-sm transition hover:border-luda-blue/30 hover:bg-white/80"
            >
                Ver sitio
                <ArrowUpRight class="size-4 text-luda-blue" />
            </a>
        </header>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <StatCard
                label="Citas de hoy"
                :value="stats.hoy"
                hint="Agenda del día"
                :icon="CalendarDays"
                tone="navy"
            />
            <StatCard
                label="Por confirmar"
                :value="stats.pendientes"
                hint="Pendientes de contacto"
                :icon="Clock3"
                tone="blue"
            />
            <StatCard
                label="Esta semana"
                :value="stats.semana"
                hint="Lunes a domingo"
                :icon="CalendarCheck"
                tone="sky"
            />
        </div>

        <section
            class="overflow-hidden rounded-2xl border border-luda-navy/5 bg-white shadow-[0_24px_70px_-55px_rgba(0,19,48,0.55)]"
        >
            <div
                class="flex flex-col gap-4 border-b border-luda-navy/5 p-4 xl:flex-row xl:items-center xl:justify-between"
            >
                <div class="flex flex-wrap items-center gap-2">
                    <button
                        v-for="filter in quickFilters"
                        :key="filter.value"
                        type="button"
                        :class="[
                            'inline-flex items-center gap-1.5 rounded-full px-3.5 py-1.5 text-xs font-semibold transition',
                            activeFilter === filter.value
                                ? 'bg-luda-navy text-white shadow-sm'
                                : 'bg-luda-mist text-luda-ink/60 hover:bg-luda-sky/20 hover:text-luda-navy',
                        ]"
                        @click="setQuickFilter(filter.value)"
                    >
                        {{ filter.label }}
                        <span
                            v-if="filter.count !== undefined"
                            :class="[
                                'rounded-full px-1.5 py-0.5 text-[10px]',
                                activeFilter === filter.value
                                    ? 'bg-white/15 text-white'
                                    : 'bg-white text-luda-blue',
                            ]"
                        >
                            {{ filter.count }}
                        </span>
                    </button>
                </div>

                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <div class="relative">
                        <Search
                            class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-luda-ink/35"
                        />
                        <input
                            v-model="search"
                            type="search"
                            placeholder="Paciente, teléfono, folio o servicio"
                            class="w-full rounded-xl border border-luda-navy/10 bg-white py-2 pr-3 pl-9 text-sm text-luda-ink shadow-sm outline-none transition placeholder:text-luda-ink/30 focus:border-luda-sky focus:ring-2 focus:ring-luda-sky/40 sm:w-64"
                            aria-label="Buscar citas"
                            @input="onSearchInput"
                        />
                    </div>

                    <input
                        v-model="dateFilter"
                        type="date"
                        class="rounded-xl border border-luda-navy/10 bg-white px-3 py-2 text-sm text-luda-ink shadow-sm outline-none transition focus:border-luda-sky focus:ring-2 focus:ring-luda-sky/40"
                        aria-label="Filtrar por fecha"
                        @change="onDateChange"
                    />

                    <button
                        v-if="hasFilters"
                        type="button"
                        class="rounded-xl border border-luda-navy/10 px-3 py-2 text-xs font-semibold text-luda-ink/60 transition hover:bg-luda-mist hover:text-luda-navy"
                        @click="clearFilters"
                    >
                        Limpiar
                    </button>
                </div>
            </div>

            <div
                v-if="appointments.data.length === 0"
                class="flex flex-col items-center justify-center gap-3 px-6 py-16 text-center"
            >
                <span
                    class="flex size-14 items-center justify-center rounded-2xl bg-luda-mist text-luda-blue"
                >
                    <CalendarDays class="size-7" />
                </span>
                <div>
                    <p class="text-sm font-semibold text-luda-navy">
                        No hay citas con estos filtros
                    </p>
                    <p class="mt-1 text-xs text-luda-ink/50">
                        Prueba con otro estado, fecha o búsqueda.
                    </p>
                </div>
                <button
                    type="button"
                    class="rounded-full bg-luda-navy px-4 py-2 text-xs font-semibold text-white transition hover:bg-luda-blue"
                    @click="clearFilters"
                >
                    Ver todas las citas
                </button>
            </div>

            <template v-else>
                <div class="hidden overflow-x-auto lg:block">
                    <table class="w-full min-w-[880px] text-left text-sm">
                        <thead
                            class="border-b border-luda-navy/5 bg-luda-mist/60 text-[10px] tracking-[0.14em] text-luda-ink/45 uppercase"
                        >
                            <tr>
                                <th class="px-5 py-3 font-semibold">Folio</th>
                                <th class="px-5 py-3 font-semibold">
                                    Paciente
                                </th>
                                <th class="px-5 py-3 font-semibold">
                                    Servicio
                                </th>
                                <th class="px-5 py-3 font-semibold">
                                    Fecha y hora
                                </th>
                                <th class="px-5 py-3 font-semibold">
                                    Estado
                                </th>
                                <th
                                    class="px-5 py-3 text-right font-semibold"
                                >
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-luda-navy/5">
                            <tr
                                v-for="appointment in appointments.data"
                                :key="appointment.id"
                                class="transition hover:bg-luda-mist/40"
                            >
                                <td class="px-5 py-4 align-top">
                                    <span
                                        class="font-mono text-xs font-semibold text-luda-blue"
                                    >
                                        {{ appointment.codigo_folio }}
                                    </span>
                                    <p
                                        v-if="appointment.notas"
                                        class="mt-1 max-w-[10rem] truncate text-[10px] text-luda-ink/40"
                                        :title="appointment.notas"
                                    >
                                        {{ appointment.notas }}
                                    </p>
                                </td>

                                <td class="px-5 py-4 align-top">
                                    <p
                                        class="font-medium text-luda-navy"
                                    >
                                        {{ appointment.paciente_nombre }}
                                    </p>
                                    <div
                                        class="mt-1 flex flex-wrap items-center gap-2 text-xs text-luda-ink/55"
                                    >
                                        <span class="flex items-center gap-1">
                                            <Phone class="size-3 text-luda-blue" />
                                            {{
                                                formatPhone(
                                                    appointment.paciente_telefono,
                                                )
                                            }}
                                        </span>
                                        <span class="text-luda-sky">•</span>
                                        <span class="flex items-center gap-1">
                                            <Baby
                                                v-if="
                                                    appointment.tipo_paciente ===
                                                    'nino'
                                                "
                                                class="size-3 text-luda-blue"
                                            />
                                            <UserRound
                                                v-else
                                                class="size-3 text-luda-blue"
                                            />
                                            {{
                                                patientTypeLabel(
                                                    appointment.tipo_paciente,
                                                )
                                            }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-5 py-4 align-top">
                                    <span
                                        class="inline-flex rounded-lg bg-luda-mist px-2 py-1 text-xs font-medium text-luda-ink/70"
                                    >
                                        {{ appointment.servicio }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 align-top">
                                    <p
                                        class="text-sm font-medium text-luda-navy"
                                    >
                                        {{
                                            formatShortDate(appointment.fecha)
                                        }}
                                    </p>
                                    <p class="text-xs text-luda-ink/55">
                                        {{ appointment.hora }} hrs
                                    </p>
                                </td>

                                <td class="px-5 py-4 align-top">
                                    <StatusBadge
                                        :estado="appointment.estado"
                                    />
                                </td>

                                <td class="px-5 py-4 align-top">
                                    <div
                                        class="flex items-center justify-end gap-1.5"
                                    >
                                        <a
                                            :href="whatsappUrl(appointment)"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="flex size-8 items-center justify-center rounded-lg border border-luda-navy/10 text-luda-ink/55 transition hover:border-emerald-200 hover:bg-emerald-50 hover:text-emerald-600"
                                            title="Enviar WhatsApp al paciente"
                                            aria-label="Enviar WhatsApp al paciente"
                                        >
                                            <MessageCircle class="size-4" />
                                        </a>

                                        <a
                                            :href="`tel:+52${appointment.paciente_telefono}`"
                                            class="flex size-8 items-center justify-center rounded-lg border border-luda-navy/10 text-luda-ink/55 transition hover:border-luda-sky hover:bg-luda-mist hover:text-luda-blue"
                                            title="Llamar al paciente"
                                            aria-label="Llamar al paciente"
                                        >
                                            <Phone class="size-4" />
                                        </a>

                                        <button
                                            v-if="
                                                appointment.estado ===
                                                'pendiente'
                                            "
                                            type="button"
                                            class="flex size-8 items-center justify-center rounded-lg border border-luda-navy/10 text-luda-ink/55 transition hover:border-emerald-200 hover:bg-emerald-50 hover:text-emerald-600"
                                            title="Confirmar cita"
                                            aria-label="Confirmar cita"
                                            @click="
                                                updateStatus(
                                                    appointment,
                                                    'confirmada',
                                                )
                                            "
                                        >
                                            <CircleCheck class="size-4" />
                                        </button>

                                        <button
                                            v-if="
                                                appointment.estado ===
                                                    'pendiente' ||
                                                appointment.estado ===
                                                    'confirmada'
                                            "
                                            type="button"
                                            class="flex size-8 items-center justify-center rounded-lg border border-luda-navy/10 text-luda-ink/55 transition hover:border-luda-sky hover:bg-luda-mist hover:text-luda-blue"
                                            title="Marcar como completada"
                                            aria-label="Marcar como completada"
                                            @click="
                                                updateStatus(
                                                    appointment,
                                                    'completada',
                                                )
                                            "
                                        >
                                            <BadgeCheck class="size-4" />
                                        </button>

                                        <button
                                            type="button"
                                            class="flex size-8 items-center justify-center rounded-lg border border-luda-navy/10 text-luda-ink/55 transition hover:border-luda-sky hover:bg-luda-mist hover:text-luda-blue"
                                            title="Reagendar cita"
                                            aria-label="Reagendar cita"
                                            @click="
                                                openReschedule(appointment)
                                            "
                                        >
                                            <CalendarClock class="size-4" />
                                        </button>

                                        <button
                                            v-if="
                                                appointment.estado !==
                                                    'cancelada' &&
                                                appointment.estado !==
                                                    'completada'
                                            "
                                            type="button"
                                            class="flex size-8 items-center justify-center rounded-lg border border-luda-navy/10 text-luda-ink/55 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600"
                                            title="Cancelar cita"
                                            aria-label="Cancelar cita"
                                            @click="
                                                cancelAppointment(appointment)
                                            "
                                        >
                                            <CircleX class="size-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <ul class="divide-y divide-luda-navy/5 lg:hidden">
                    <li
                        v-for="appointment in appointments.data"
                        :key="appointment.id"
                        class="p-4"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p
                                    class="font-mono text-[11px] font-semibold text-luda-blue"
                                >
                                    {{ appointment.codigo_folio }}
                                </p>
                                <p
                                    class="mt-1 truncate text-sm font-semibold text-luda-navy"
                                >
                                    {{ appointment.paciente_nombre }}
                                </p>
                                <p class="mt-0.5 text-xs text-luda-ink/55">
                                    {{
                                        formatPhone(
                                            appointment.paciente_telefono,
                                        )
                                    }}
                                    · {{ patientTypeLabel(appointment.tipo_paciente) }}
                                </p>
                            </div>
                            <StatusBadge :estado="appointment.estado" />
                        </div>

                        <div
                            class="mt-3 flex flex-wrap items-center justify-between gap-2 rounded-xl bg-luda-mist px-3 py-2 text-xs"
                        >
                            <span class="font-medium text-luda-navy">
                                {{ formatShortDate(appointment.fecha) }} ·
                                {{ appointment.hora }} hrs
                            </span>
                            <span class="text-luda-ink/55">
                                {{ appointment.servicio }}
                            </span>
                        </div>

                        <div class="mt-3 flex items-center justify-end gap-2">
                            <a
                                :href="whatsappUrl(appointment)"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700"
                            >
                                <MessageCircle class="size-3.5" />
                                WhatsApp
                            </a>
                            <button
                                v-if="appointment.estado === 'pendiente'"
                                type="button"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-luda-navy/10 px-3 py-1.5 text-xs font-semibold text-luda-navy"
                                @click="updateStatus(appointment, 'confirmada')"
                            >
                                <CircleCheck class="size-3.5" />
                                Confirmar
                            </button>
                            <button
                                type="button"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-luda-navy/10 px-3 py-1.5 text-xs font-semibold text-luda-navy"
                                @click="openReschedule(appointment)"
                            >
                                <CalendarClock class="size-3.5" />
                                Reagendar
                            </button>
                        </div>
                    </li>
                </ul>

                <div
                    v-if="appointments.last_page > 1"
                    class="flex flex-col items-center justify-between gap-3 border-t border-luda-navy/5 px-4 py-3 sm:flex-row"
                >
                    <p class="text-xs text-luda-ink/50">
                        Mostrando {{ appointments.from }}–{{
                            appointments.to
                        }}
                        de {{ appointments.total }} citas
                    </p>

                    <div class="flex flex-wrap items-center gap-1">
                        <template
                            v-for="link in paginationLinks"
                            :key="link.label"
                        >
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                preserve-scroll
                                :class="[
                                    'flex h-8 min-w-8 items-center justify-center rounded-lg border px-2 text-xs font-medium transition',
                                    link.active
                                        ? 'border-luda-navy bg-luda-navy text-white'
                                        : 'border-luda-navy/10 text-luda-ink/60 hover:bg-luda-mist hover:text-luda-navy',
                                ]"
                            >
                                {{ link.label }}
                            </Link>
                            <span
                                v-else
                                class="flex h-8 min-w-8 items-center justify-center rounded-lg border border-transparent px-2 text-xs text-luda-ink/25"
                            >
                                {{ link.label }}
                            </span>
                        </template>
                    </div>
                </div>
            </template>
        </section>

        <RescheduleDialog
            :appointment="selectedAppointment"
            :weekly-slots="weeklySlots"
            :occupied-slots="occupiedSlots"
            :clinic="clinic"
            :today="today"
            @close="selectedAppointment = null"
        />
    </div>
</template>
