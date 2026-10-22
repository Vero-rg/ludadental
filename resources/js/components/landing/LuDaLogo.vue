<script setup lang="ts">
import { computed } from 'vue';

const props = withDefaults(
    defineProps<{
        tone?: 'dark' | 'light';
    }>(),
    {
        tone: 'dark',
    },
);

const wordmarkClass = computed(() =>
    props.tone === 'light' ? 'text-white' : 'text-luda-navy',
);

const accentClass = computed(() =>
    props.tone === 'light' ? 'text-luda-sky' : 'text-luda-blue',
);

const taglineClass = computed(() =>
    props.tone === 'light' ? 'text-white/50' : 'text-luda-blue/70',
);
</script>

<template>
    <div class="flex items-center gap-3">
        <span
            class="flex size-11 shrink-0 items-center justify-center rounded-2xl bg-linear-to-br from-luda-blue to-luda-navy shadow-lg shadow-luda-navy/25 ring-1 ring-white/25"
        >
            <svg
                viewBox="0 0 24 24"
                class="size-6 text-white"
                fill="currentColor"
                aria-hidden="true"
            >
                <path
                    d="M12 2.7c-1.9 0-2.8.7-4.1.7-1.5 0-3.1 1-3.1 3.3 0 2 .7 3.9 1.3 5.8.6 1.9.7 8.1 2.5 8.1 1.5 0 1.4-5 3.4-5s1.9 5 3.4 5c1.8 0 1.9-6.2 2.5-8.1.6-1.9 1.3-3.8 1.3-5.8 0-2.3-1.6-3.3-3.1-3.3-1.3 0-2.2-.7-4.1-.7Z"
                />
            </svg>
        </span>
        <span class="flex min-w-0 flex-col leading-none">
            <span
                :class="[
                    'text-xl font-semibold tracking-tight whitespace-nowrap',
                    wordmarkClass,
                ]"
            >
                LuDa<span :class="accentClass">Dental</span>
            </span>
            <span
                :class="[
                    'mt-1 text-[9px] font-semibold tracking-[0.3em] uppercase',
                    taglineClass,
                ]"
            >
                Clínica dental
            </span>
        </span>
    </div>
</template>
