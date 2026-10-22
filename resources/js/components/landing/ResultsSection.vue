<script setup lang="ts">
import { ArrowRight, Sparkles } from '@lucide/vue';
import BeforeAfterSlider from './BeforeAfterSlider.vue';
import type { TreatmentResultRecord } from '@/types';

defineProps<{
    results: TreatmentResultRecord[];
}>();
</script>

<template>
    <section id="resultados" class="relative overflow-hidden bg-luda-mist py-20 sm:py-24">
        <div
            class="pointer-events-none absolute -top-24 right-0 size-72 rounded-full bg-luda-sky/20 blur-3xl"
            aria-hidden="true"
        ></div>
        <div
            class="pointer-events-none absolute bottom-0 -left-24 size-72 rounded-full bg-luda-blue/10 blur-3xl"
            aria-hidden="true"
        ></div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6">
            <div
                class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between"
            >
                <div class="max-w-2xl">
                    <p
                        class="inline-flex items-center gap-2 rounded-full border border-luda-blue/15 bg-white/80 px-3.5 py-1.5 text-[11px] font-semibold tracking-[0.18em] text-luda-blue uppercase shadow-sm backdrop-blur"
                    >
                        <Sparkles class="size-3.5" />
                        Antes y después
                    </p>
                    <h2
                        class="mt-4 text-3xl font-semibold tracking-tight text-luda-navy sm:text-4xl"
                    >
                        Resultados que hablan por sí solos
                    </h2>
                    <p class="mt-4 text-base leading-relaxed text-luda-ink/65">
                        Conoce algunos de los trabajos que realizamos en LuDa
                        Dental. Desliza cada imagen para comparar el antes y el
                        después de nuestros tratamientos.
                    </p>
                </div>

                <a
                    href="#agendar"
                    class="group inline-flex w-fit items-center gap-2 rounded-full bg-luda-navy px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-luda-navy/20 transition hover:bg-luda-blue"
                >
                    Quiero mi valoración
                    <ArrowRight
                        class="size-4 transition-transform group-hover:translate-x-0.5"
                    />
                </a>
            </div>

            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <article
                    v-for="result in results"
                    :key="result.id"
                    class="group overflow-hidden rounded-3xl border border-luda-navy/5 bg-white shadow-[0_24px_70px_-52px_rgba(0,19,48,0.5)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_30px_70px_-40px_rgba(17,91,138,0.35)]"
                >
                    <BeforeAfterSlider
                        :before="result.imagen_antes"
                        :after="result.imagen_despues"
                        :alt="result.titulo"
                    />

                    <div class="p-5">
                        <span
                            v-if="result.servicio"
                            class="inline-flex rounded-full bg-luda-sky/20 px-2.5 py-1 text-[10px] font-semibold tracking-[0.12em] text-luda-blue uppercase"
                        >
                            {{ result.servicio }}
                        </span>
                        <h3 class="mt-2 text-lg font-semibold text-luda-navy">
                            {{ result.titulo }}
                        </h3>
                        <p class="mt-2 text-sm leading-relaxed text-luda-ink/60">
                            {{ result.descripcion }}
                        </p>
                    </div>
                </article>
            </div>

            <p class="mt-10 text-center text-[11px] leading-relaxed text-luda-ink/40">
                Imágenes de casos reales compartidas con autorización de
                nuestros pacientes. Los resultados pueden variar según cada
                caso.
            </p>
        </div>
    </section>
</template>
