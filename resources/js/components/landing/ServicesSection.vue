<script setup lang="ts">
import type { Component } from 'vue';
import {
    ArrowUpRight,
    Baby,
    Crown,
    HeartPulse,
    Scissors,
    Smile,
    Sparkles,
    Stethoscope,
    Syringe,
} from '@lucide/vue';

defineProps<{
    services: string[];
}>();

const emit = defineEmits<{
    select: [service: string];
}>();

const icons: Record<string, Component> = {
    Ortodoncia: Smile,
    Endodoncia: Syringe,
    'Rehabilitación Oral': Crown,
    Blanqueamiento: Sparkles,
    Odontopediatría: Baby,
    'Cirugía Oral': Scissors,
    Periodoncia: HeartPulse,
};

const descriptions: Record<string, string> = {
    Ortodoncia:
        'Alineamos tus dientes y corregimos la mordida con brackets y alineadores.',
    Endodoncia:
        'Tratamiento de conductos para conservar tus piezas dentales y aliviar el dolor.',
    'Rehabilitación Oral':
        'Recuperamos función y estética con coronas, prótesis e implantes.',
    Blanqueamiento:
        'Aclara el tono de tu sonrisa con tratamientos seguros y progresivos.',
    Odontopediatría:
        'Atención amable y especializada para las primeras sonrisas de la casa.',
    'Cirugía Oral':
        'Extracciones y procedimientos quirúrgicos con protocolos seguros.',
    Periodoncia:
        'Cuidamos las encías y los tejidos que sostienen tu sonrisa.',
};
</script>

<template>
    <section id="servicios" class="relative bg-white py-20 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <div class="mx-auto max-w-2xl text-center">
                <p
                    class="text-xs font-semibold tracking-[0.28em] text-luda-blue uppercase"
                >
                    Nuestros servicios
                </p>
                <h2
                    class="mt-3 text-3xl font-semibold tracking-tight text-luda-navy sm:text-4xl"
                >
                    Especialidades para cada etapa de tu sonrisa
                </h2>
                <p class="mt-4 text-base leading-relaxed text-luda-ink/65">
                    Un equipo multidisciplinario y tecnología actual para que
                    recibas el tratamiento que necesitas en un solo lugar.
                </p>
            </div>

            <div class="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <article
                    v-for="(service, index) in services"
                    :key="service"
                    class="group relative flex flex-col rounded-3xl border border-luda-navy/5 bg-white p-6 shadow-[0_20px_60px_-44px_rgba(0,19,48,0.4)] transition duration-300 hover:-translate-y-1 hover:border-luda-sky/60 hover:shadow-[0_28px_60px_-30px_rgba(17,91,138,0.35)]"
                >
                    <span
                        class="absolute top-6 right-6 text-xs font-semibold text-luda-navy/10"
                    >
                        0{{ index + 1 }}
                    </span>
                    <span
                        class="flex size-12 items-center justify-center rounded-2xl bg-luda-mist text-luda-blue transition duration-300 group-hover:bg-luda-blue group-hover:text-white"
                    >
                        <component
                            :is="icons[service] ?? Stethoscope"
                            class="size-6"
                        />
                    </span>
                    <h3 class="mt-5 text-lg font-semibold text-luda-navy">
                        {{ service }}
                    </h3>
                    <p
                        class="mt-2 flex-1 text-sm leading-relaxed text-luda-ink/60"
                    >
                        {{ descriptions[service] ?? '' }}
                    </p>
                    <a
                        href="#agendar"
                        class="mt-5 inline-flex items-center gap-1.5 text-sm font-semibold text-luda-blue transition hover:gap-2.5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-luda-blue/30"
                        @click="emit('select', service)"
                    >
                        Agendar este servicio
                        <ArrowUpRight class="size-4" />
                    </a>
                </article>

                <article
                    class="relative flex flex-col justify-between overflow-hidden rounded-3xl bg-luda-navy p-6 text-white shadow-xl shadow-luda-navy/20"
                >
                    <div
                        class="absolute -top-16 -right-10 size-40 rounded-full bg-luda-sky/20 blur-2xl"
                        aria-hidden="true"
                    ></div>
                    <div class="relative">
                        <span
                            class="flex size-12 items-center justify-center rounded-2xl bg-white/10 text-luda-sky ring-1 ring-white/15"
                        >
                            <Sparkles class="size-6" />
                        </span>
                        <h3 class="mt-5 text-lg font-semibold">
                            ¿No sabes qué necesitas?
                        </h3>
                        <p class="mt-2 text-sm leading-relaxed text-white/60">
                            Agenda una valoración y te ayudamos a elegir el
                            tratamiento ideal para ti.
                        </p>
                    </div>
                    <a
                        href="#agendar"
                        class="relative mt-5 inline-flex items-center gap-2 text-sm font-semibold text-luda-sky transition hover:text-white"
                    >
                        Solicitar valoración
                        <ArrowUpRight class="size-4" />
                    </a>
                </article>
            </div>
        </div>
    </section>
</template>
