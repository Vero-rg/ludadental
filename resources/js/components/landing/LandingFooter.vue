<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import {
    ArrowUpRight,
    Clock,
    MapPin,
    MessageCircle,
    Phone,
} from '@lucide/vue';
import BrandLogo from './BrandLogo.vue';
import { buildWhatsAppUrl } from '@/lib/whatsapp';
import type { ClinicBranding, ClinicInfo, OpeningHour } from '@/types';

const props = defineProps<{
    clinic: ClinicInfo;
    openingHours: OpeningHour[];
    branding: ClinicBranding;
}>();

const year = new Date().getFullYear();

const whatsappUrl = computed(() =>
    buildWhatsAppUrl(
        props.clinic.whatsapp,
        `Hola ${props.clinic.name}, me gustaría más información sobre sus tratamientos.`,
    ),
);
</script>

<template>
    <footer id="contacto" class="relative overflow-hidden bg-luda-navy text-white">
        <div
            class="pointer-events-none absolute -top-32 right-0 size-96 rounded-full bg-luda-blue/30 blur-3xl"
            aria-hidden="true"
        ></div>
        <div
            class="pointer-events-none absolute -bottom-40 left-0 size-96 rounded-full bg-luda-sky/10 blur-3xl"
            aria-hidden="true"
        ></div>

        <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-20">
            <div class="grid gap-12 lg:grid-cols-[1.05fr_0.95fr_1fr]">
                <div>
                    <Link
                        href="/login"
                        class="inline-flex rounded-xl outline-none transition hover:opacity-90 focus-visible:ring-2 focus-visible:ring-luda-sky/60"
                        aria-label="Ir al inicio de sesión"
                    >
                        <BrandLogo
                            :src="branding.footer_logo"
                            :alt="branding.name"
                            tone="light"
                            img-class="h-14 w-auto max-w-[240px] object-contain"
                        />
                    </Link>
                    <p class="mt-5 max-w-sm text-sm leading-relaxed text-white/60">
                        Especialidades odontológicas para toda la familia en
                        Zapopan. Tecnología, calidez y un equipo que cuida cada
                        detalle de tu sonrisa.
                    </p>

                    <div class="mt-6 flex flex-wrap gap-2">
                        <a
                            v-for="phone in clinic.phones"
                            :key="phone.number"
                            :href="`tel:+52${phone.number}`"
                            class="inline-flex items-center gap-2 rounded-full border border-white/15 px-4 py-2 text-sm font-medium text-white/80 transition hover:border-luda-sky/50 hover:bg-white/10 hover:text-white"
                        >
                            <Phone class="size-4 text-luda-sky" />
                            {{ phone.label }}
                        </a>
                    </div>

                    <a
                        :href="whatsappUrl"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="mt-4 inline-flex items-center gap-2 rounded-full bg-[#25D366] px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-emerald-500/20 transition hover:bg-[#1ebe5b]"
                    >
                        <MessageCircle class="size-4" />
                        Escríbenos por WhatsApp
                    </a>
                </div>

                <div>
                    <h3
                        class="flex items-center gap-2 text-xs font-semibold tracking-[0.22em] text-luda-sky uppercase"
                    >
                        <Clock class="size-4" />
                        Horarios
                    </h3>
                    <ul class="mt-5 space-y-3">
                        <li
                            v-for="hour in openingHours"
                            :key="hour.days"
                            class="flex items-center justify-between gap-4 border-b border-white/10 pb-3 text-sm"
                        >
                            <span class="text-white/55">{{ hour.days }}</span>
                            <span
                                :class="[
                                    'font-medium',
                                    hour.open ? 'text-white' : 'text-white/35',
                                ]"
                            >
                                {{ hour.time }}
                            </span>
                        </li>
                    </ul>
                    <p class="mt-4 text-[11px] leading-relaxed text-white/40">
                        Horarios en hora local de Zapopan. La agenda en línea
                        muestra únicamente los espacios disponibles.
                    </p>
                </div>

                <div>
                    <h3
                        class="flex items-center gap-2 text-xs font-semibold tracking-[0.22em] text-luda-sky uppercase"
                    >
                        <MapPin class="size-4" />
                        Ubicación
                    </h3>
                    <p class="mt-5 text-sm font-medium text-white">
                        {{ clinic.address.line1 }}
                    </p>
                    <p class="text-sm text-white/55">
                        {{ clinic.address.line2 }}
                    </p>
                    <p class="mt-2 text-xs leading-relaxed text-white/40">
                        {{ clinic.address.reference }}
                    </p>

                    <a
                        :href="clinic.map.link"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="mt-3 inline-flex items-center gap-1.5 text-sm font-semibold text-luda-sky transition hover:text-white"
                    >
                        Cómo llegar
                        <ArrowUpRight class="size-4" />
                    </a>

                    <div
                        class="mt-5 overflow-hidden rounded-2xl border border-white/10 shadow-lg shadow-black/20"
                    >
                        <iframe
                            :src="clinic.map.embed_url"
                            title="Ubicación de LuDa Dental en Plaza Península, Zapopan"
                            class="h-44 w-full"
                            style="border: 0"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            allowfullscreen
                        ></iframe>
                    </div>
                </div>
            </div>

            <div
                class="mt-14 flex flex-col items-center justify-between gap-3 border-t border-white/10 pt-6 text-xs text-white/35 sm:flex-row"
            >
                <p>
                    © {{ year }} {{ clinic.name }}. Todos los derechos
                    reservados.
                </p>
                <p>{{ clinic.tagline }} · Zapopan, Jalisco</p>
            </div>
        </div>
    </footer>
</template>
