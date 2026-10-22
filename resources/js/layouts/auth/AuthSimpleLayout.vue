<script setup lang="ts">
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import {
    CalendarCheck,
    Check,
    Clock,
    Images,
    MessageCircle,
    Sparkles,
} from '@lucide/vue';
import BrandLogo from '@/components/landing/BrandLogo.vue';

defineProps<{
    title?: string;
    description?: string;
}>();

const page = usePage();
const branding = computed(() => page.props.branding);
const year = new Date().getFullYear();

const features = [
    {
        icon: CalendarCheck,
        text: 'Agenda y confirma citas en tiempo real',
    },
    {
        icon: Images,
        text: 'Publica resultados antes y después',
    },
    {
        icon: MessageCircle,
        text: 'Da seguimiento a tus pacientes por WhatsApp',
    },
];
</script>

<template>
    <div
        class="relative grid min-h-svh bg-luda-mist lg:grid-cols-[1.05fr_1fr]"
    >
        <aside
            class="relative hidden overflow-hidden bg-luda-navy p-12 text-white lg:flex lg:flex-col lg:justify-between xl:p-16"
        >
            <div
                class="pointer-events-none absolute -top-32 -right-24 size-96 rounded-full bg-luda-blue/40 blur-3xl"
                aria-hidden="true"
            ></div>
            <div
                class="pointer-events-none absolute -bottom-40 -left-24 size-96 rounded-full bg-luda-sky/15 blur-3xl"
                aria-hidden="true"
            ></div>
            <div
                class="pointer-events-none absolute inset-0 bg-[linear-gradient(to_right,rgba(255,255,255,0.04)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.04)_1px,transparent_1px)] [background-size:56px_56px] [mask-image:radial-gradient(ellipse_at_center,black,transparent_75%)]"
                aria-hidden="true"
            ></div>

            <div class="relative">
                <span
                    class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3.5 py-1.5 text-[10px] font-semibold tracking-[0.22em] text-luda-sky uppercase"
                >
                    <Sparkles class="size-3.5" />
                    Panel administrativo
                </span>

                <h2
                    class="mt-8 max-w-md text-4xl leading-[1.08] font-semibold tracking-tight xl:text-5xl"
                >
                    Tu clínica,
                    <br />
                    organizada y
                    <br />
                    <span class="text-luda-sky">en calma.</span>
                </h2>

                <p class="mt-5 max-w-sm text-sm leading-relaxed text-white/60">
                    Gestiona la agenda, confirma citas y comparte los
                    resultados de tus tratamientos desde un solo lugar.
                </p>

                <ul class="mt-10 space-y-4">
                    <li
                        v-for="feature in features"
                        :key="feature.text"
                        class="flex items-start gap-3 text-sm text-white/75"
                    >
                        <span
                            class="mt-0.5 flex size-7 shrink-0 items-center justify-center rounded-lg bg-white/10 text-luda-sky ring-1 ring-white/10"
                        >
                            <component :is="feature.icon" class="size-4" />
                        </span>
                        {{ feature.text }}
                    </li>
                </ul>
            </div>

            <div
                class="relative flex flex-wrap items-center gap-x-6 gap-y-2 text-[11px] text-white/40"
            >
                <span class="flex items-center gap-1.5">
                    <Clock class="size-3.5 text-luda-sky" />
                    Lun a Vie 9:00 – 17:00
                </span>
                <span class="flex items-center gap-1.5">
                    <Check class="size-3.5 text-luda-sky" />
                    Sábados 9:00 – 13:00
                </span>
            </div>
        </aside>

        <main
            class="relative flex min-h-svh items-center justify-center px-4 py-10 sm:px-8"
        >
            <div
                class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_80%_0%,rgba(147,188,208,0.32),transparent_55%)]"
                aria-hidden="true"
            ></div>

            <div class="relative w-full max-w-md">
                <Link
                    href="/"
                    class="mb-7 flex justify-center rounded-2xl outline-none transition hover:opacity-90 focus-visible:ring-2 focus-visible:ring-luda-blue/40"
                    aria-label="Volver al sitio de LuDa Dental"
                >
                    <BrandLogo
                        :src="branding?.header_logo ?? ''"
                        :alt="branding?.name ?? 'LuDa Dental'"
                        img-class="h-12 w-auto max-w-[210px] object-contain"
                    />
                </Link>

                <div
                    class="rounded-3xl border border-luda-navy/5 bg-white p-6 shadow-[0_30px_80px_-50px_rgba(0,19,48,0.55)] sm:p-8"
                >
                    <div class="space-y-2">
                        <h1
                            v-if="title"
                            class="text-2xl font-semibold tracking-tight text-luda-navy"
                        >
                            {{ title }}
                        </h1>
                        <p
                            v-if="description"
                            class="text-sm leading-relaxed text-luda-ink/55"
                        >
                            {{ description }}
                        </p>
                    </div>

                    <div class="mt-7">
                        <slot />
                    </div>
                </div>

                <p
                    class="mt-6 text-center text-[11px] leading-relaxed text-luda-ink/40"
                >
                    © {{ year }} {{ branding?.name ?? 'LuDa Dental' }} · Acceso
                    exclusivo para personal autorizado.
                </p>
            </div>
        </main>
    </div>
</template>

