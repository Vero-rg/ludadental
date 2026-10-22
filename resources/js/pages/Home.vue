<script setup lang="ts">
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import BookingSection from '@/components/landing/BookingSection.vue';
import CareSection from '@/components/landing/CareSection.vue';
import HeroSection from '@/components/landing/HeroSection.vue';
import LandingFooter from '@/components/landing/LandingFooter.vue';
import LandingHeader from '@/components/landing/LandingHeader.vue';
import ResultsSection from '@/components/landing/ResultsSection.vue';
import ServicesSection from '@/components/landing/ServicesSection.vue';
import type {
    ClinicBranding,
    ClinicInfo,
    OccupiedSlot,
    OpeningHour,
    PatientType,
    TreatmentResultRecord,
    WeeklySlots,
} from '@/types';

defineProps<{
    services: string[];
    weeklySlots: WeeklySlots;
    openingHours: OpeningHour[];
    occupiedSlots: OccupiedSlot[];
    clinic: ClinicInfo;
    branding: ClinicBranding;
    results: TreatmentResultRecord[];
    today: string;
}>();

const preselectedService = ref('');
const preselectedType = ref<PatientType>('adulto');

function selectService(service: string): void {
    preselectedService.value = service;
}

function selectType(type: PatientType): void {
    preselectedType.value = type;
}
</script>

<template>
    <Head title="Odontología de especialidades en Zapopan">
        <meta
            name="description"
            content="LuDa Dental, clínica de especialidades odontológicas en Plaza Península, Zapopan. Ortodoncia, endodoncia, odontopediatría y más. Agenda tu cita en línea."
        />
        <meta name="theme-color" content="#001330" />
    </Head>

    <div class="min-h-screen bg-luda-mist font-sans text-luda-ink antialiased">
        <LandingHeader :phones="clinic.phones" :branding="branding" />

        <main>
            <HeroSection :phone="clinic.phones[0]" />
            <ServicesSection
                :services="services"
                @select="selectService"
            />
            <CareSection @select-type="selectType" />
            <BookingSection
                :services="services"
                :weekly-slots="weeklySlots"
                :occupied-slots="occupiedSlots"
                :clinic="clinic"
                :today="today"
                :preselected-service="preselectedService"
                :preselected-type="preselectedType"
            />
            <ResultsSection v-if="results.length > 0" :results="results" />
        </main>

        <LandingFooter
            :clinic="clinic"
            :opening-hours="openingHours"
            :branding="branding"
        />
    </div>
</template>
