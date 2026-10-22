<script setup lang="ts">
import type { LucideIcon } from '@lucide/vue';

withDefaults(
    defineProps<{
        label: string;
        value: number;
        hint: string;
        icon: LucideIcon;
        tone?: 'navy' | 'blue' | 'sky';
    }>(),
    {
        tone: 'blue',
    },
);

const toneClasses: Record<'navy' | 'blue' | 'sky', string> = {
    navy: 'bg-luda-navy text-white',
    blue: 'bg-luda-blue text-white',
    sky: 'bg-luda-sky/25 text-luda-blue',
};
</script>

<template>
    <div
        class="relative overflow-hidden rounded-2xl border border-luda-navy/5 bg-white p-5 shadow-[0_18px_45px_-36px_rgba(0,19,48,0.5)]"
    >
        <div
            class="absolute -top-10 -right-8 size-24 rounded-full bg-luda-mist"
            aria-hidden="true"
        ></div>
        <div class="relative flex items-start justify-between gap-4">
            <div>
                <p
                    class="text-[11px] font-semibold tracking-[0.16em] text-luda-ink/45 uppercase"
                >
                    {{ label }}
                </p>
                <p class="mt-3 text-3xl font-semibold text-luda-navy">
                    {{ value }}
                </p>
                <p class="mt-1 text-xs text-luda-ink/50">{{ hint }}</p>
            </div>
            <span
                :class="[
                    'flex size-11 shrink-0 items-center justify-center rounded-xl shadow-sm',
                    toneClasses[tone],
                ]"
            >
                <component :is="icon" class="size-5" />
            </span>
        </div>
    </div>
</template>
