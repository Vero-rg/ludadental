<script setup lang="ts">
import {
    ArrowRight,
    CalendarDays,
    Clock,
    Phone,
    ShieldCheck,
    Sparkles,
    Star,
    Stethoscope,
} from '@lucide/vue';
import type { ClinicPhone } from '@/types';

defineProps<{
    phone: ClinicPhone;
}>();
</script>

<template>
    <section
        id="inicio"
        class="relative overflow-hidden bg-luda-mist pt-32 pb-20 sm:pt-40 lg:pb-28"
    >
        <div
            class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_15%_-10%,rgba(147,188,208,0.35),transparent_55%),radial-gradient(circle_at_85%_15%,rgba(17,91,138,0.16),transparent_45%)]"
            aria-hidden="true"
        ></div>
        <div
            class="pointer-events-none absolute inset-x-0 top-24 mx-auto h-[520px] max-w-6xl bg-[linear-gradient(to_right,rgba(0,19,48,0.05)_1px,transparent_1px),linear-gradient(to_bottom,rgba(0,19,48,0.05)_1px,transparent_1px)] [background-size:64px_64px] [mask-image:radial-gradient(ellipse_at_center,black,transparent_72%)]"
            aria-hidden="true"
        ></div>

        <div
            class="relative mx-auto grid max-w-7xl items-center gap-14 px-4 sm:px-6 lg:grid-cols-[1.05fr_0.95fr]"
        >
            <div>
                <span
                    class="inline-flex items-center gap-2 rounded-full border border-luda-blue/15 bg-white/80 px-4 py-1.5 text-[11px] font-semibold tracking-[0.18em] text-luda-blue uppercase shadow-sm backdrop-blur"
                >
                    <Sparkles class="size-3.5" />
                    Especialidades en Zapopan
                </span>

                <h1
                    class="mt-6 text-4xl leading-[1.05] font-semibold tracking-tight text-luda-navy sm:text-5xl lg:text-6xl"
                >
                    Tu sonrisa merece
                    <span class="relative inline-block text-luda-blue">
                        cuidado de especialistas.
                        <svg
                            class="absolute -bottom-2 left-0 h-3 w-full text-luda-sky"
                            viewBox="0 0 300 12"
                            fill="none"
                            preserveAspectRatio="none"
                            aria-hidden="true"
                        >
                            <path
                                d="M2 9c60-7 120-7 180-4 40 2 78 1 116-3"
                                stroke="currentColor"
                                stroke-width="4"
                                stroke-linecap="round"
                                opacity="0.7"
                            />
                        </svg>
                    </span>
                </h1>

                <p
                    class="mt-7 max-w-xl text-base leading-relaxed text-luda-ink/70 sm:text-lg"
                >
                    En LuDa Dental unimos tecnología, calidez y especialistas
                    certificados para cuidar de tu sonrisa y la de tu familia,
                    en el corazón de Zapopan.
                </p>

                <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                    <a
                        href="#agendar"
                        class="group inline-flex items-center justify-center gap-2 rounded-full bg-luda-navy px-7 py-3.5 text-sm font-semibold text-white shadow-xl shadow-luda-navy/25 transition hover:bg-luda-blue focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-luda-blue/50 focus-visible:ring-offset-2"
                    >
                        Agendar mi cita
                        <ArrowRight
                            class="size-4 transition-transform group-hover:translate-x-0.5"
                        />
                    </a>
                    <a
                        :href="`tel:+52${phone.number}`"
                        class="inline-flex items-center justify-center gap-2 rounded-full border border-luda-navy/15 bg-white/80 px-7 py-3.5 text-sm font-semibold text-luda-navy backdrop-blur transition hover:border-luda-blue/30 hover:bg-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-luda-blue/30"
                    >
                        <Phone class="size-4 text-luda-blue" />
                        {{ phone.label }}
                    </a>
                </div>

                <dl
                    class="mt-10 grid max-w-xl grid-cols-1 gap-4 sm:grid-cols-3"
                >
                    <div class="flex items-center gap-3">
                        <span
                            class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-white text-luda-blue shadow-sm ring-1 ring-luda-navy/5"
                        >
                            <ShieldCheck class="size-5" />
                        </span>
                        <div>
                            <dt class="text-sm font-semibold text-luda-navy">
                                Atención integral
                            </dt>
                            <dd class="text-xs text-luda-ink/55">
                                7 especialidades
                            </dd>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span
                            class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-white text-luda-blue shadow-sm ring-1 ring-luda-navy/5"
                        >
                            <Stethoscope class="size-5" />
                        </span>
                        <div>
                            <dt class="text-sm font-semibold text-luda-navy">
                                Niños y adultos
                            </dt>
                            <dd class="text-xs text-luda-ink/55">
                                Planes a tu medida
                            </dd>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span
                            class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-white text-luda-blue shadow-sm ring-1 ring-luda-navy/5"
                        >
                            <CalendarDays class="size-5" />
                        </span>
                        <div>
                            <dt class="text-sm font-semibold text-luda-navy">
                                Agenda en línea
                            </dt>
                            <dd class="text-xs text-luda-ink/55">
                                Confirma en segundos
                            </dd>
                        </div>
                    </div>
                </dl>
            </div>

            <div class="relative mx-auto w-full max-w-md lg:max-w-none">
                <div
                    class="absolute top-10 -left-6 z-10 hidden w-44 rounded-2xl border border-white/70 bg-white/95 p-4 shadow-xl shadow-luda-navy/10 backdrop-blur sm:block"
                >
                    <div class="flex items-center gap-2 text-luda-blue">
                        <Clock class="size-4" />
                        <p class="text-xs font-semibold">Lun a Vie</p>
                    </div>
                    <p class="mt-2 text-lg font-semibold text-luda-navy">
                        9:00 – 17:00
                    </p>
                    <p class="mt-1 text-[11px] text-luda-ink/55">
                        Sábados hasta las 13:00
                    </p>
                </div>

                <div
                    class="relative overflow-hidden rounded-[2.5rem] bg-linear-to-br from-luda-navy via-[#03244a] to-luda-blue p-7 shadow-2xl shadow-luda-navy/30 sm:p-9"
                >
                    <div
                        class="absolute -top-24 -right-16 size-64 rounded-full bg-luda-sky/25 blur-3xl"
                        aria-hidden="true"
                    ></div>
                    <div
                        class="absolute -bottom-24 -left-10 size-56 rounded-full bg-luda-blue/40 blur-3xl"
                        aria-hidden="true"
                    ></div>

                    <div class="relative flex items-center justify-between">
                        <span
                            class="rounded-full bg-white/10 px-3 py-1 text-[10px] font-semibold tracking-[0.25em] text-luda-sky uppercase ring-1 ring-white/15"
                        >
                            LuDa Dental
                        </span>
                        <div class="flex -space-x-2">
                            <span
                                class="flex size-8 items-center justify-center rounded-full border-2 border-luda-navy bg-luda-sky text-[10px] font-bold text-luda-navy"
                            >
                                A
                            </span>
                            <span
                                class="flex size-8 items-center justify-center rounded-full border-2 border-luda-navy bg-white/85 text-[10px] font-bold text-luda-blue"
                            >
                                L
                            </span>
                            <span
                                class="flex size-8 items-center justify-center rounded-full border-2 border-luda-navy bg-luda-blue text-[10px] font-bold text-white"
                            >
                                D
                            </span>
                        </div>
                    </div>

                    <div
                        class="relative mx-auto mt-8 flex size-52 items-center justify-center rounded-full border border-white/10 bg-white/5 sm:size-60"
                    >
                        <span
                            class="absolute inset-6 rounded-full border border-dashed border-luda-sky/30"
                            aria-hidden="true"
                        ></span>
                        <span
                            class="absolute inset-12 rounded-full bg-luda-sky/10 blur-xl"
                            aria-hidden="true"
                        ></span>
                        <svg
                            viewBox="0 0 24 24"
                            class="relative size-24 text-white drop-shadow-[0_18px_30px_rgba(0,0,0,0.25)] sm:size-28"
                            fill="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                d="M12 2.7c-1.9 0-2.8.7-4.1.7-1.5 0-3.1 1-3.1 3.3 0 2 .7 3.9 1.3 5.8.6 1.9.7 8.1 2.5 8.1 1.5 0 1.4-5 3.4-5s1.9 5 3.4 5c1.8 0 1.9-6.2 2.5-8.1.6-1.9 1.3-3.8 1.3-5.8 0-2.3-1.6-3.3-3.1-3.3-1.3 0-2.2-.7-4.1-.7Z"
                                opacity="0.92"
                            />
                        </svg>
                    </div>

                    <div class="relative mt-8 grid grid-cols-2 gap-3">
                        <div
                            class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/10 backdrop-blur"
                        >
                            <Stethoscope class="size-5 text-luda-sky" />
                            <p class="mt-2 text-sm font-semibold text-white">
                                7 especialidades
                            </p>
                            <p class="text-[11px] text-white/55">
                                En un solo lugar
                            </p>
                        </div>
                        <div
                            class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/10 backdrop-blur"
                        >
                            <Star class="size-5 text-luda-sky" />
                            <p class="mt-2 text-sm font-semibold text-white">
                                Trato cálido
                            </p>
                            <p class="text-[11px] text-white/55">
                                Para toda la familia
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="absolute -right-2 -bottom-6 z-10 hidden items-center gap-3 rounded-2xl border border-white/70 bg-white/95 px-4 py-3 shadow-xl shadow-luda-navy/10 backdrop-blur sm:flex lg:-right-6"
                >
                    <span
                        class="flex size-10 items-center justify-center rounded-xl bg-luda-mist text-luda-blue"
                    >
                        <CalendarDays class="size-5" />
                    </span>
                    <div>
                        <p class="text-xs font-semibold text-luda-navy">
                            Agenda en línea
                        </p>
                        <p class="text-[11px] text-luda-ink/55">
                            Confirma en segundos
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
