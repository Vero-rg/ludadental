<script setup lang="ts">
import { ref } from 'vue';
import { MoveHorizontal } from '@lucide/vue';

withDefaults(
    defineProps<{
        before: string;
        after: string;
        alt?: string;
    }>(),
    {
        alt: 'Resultado del tratamiento',
    },
);

const position = ref(50);
</script>

<template>
    <div
        class="relative aspect-[4/3] overflow-hidden bg-luda-mist select-none"
    >
        <img
            :src="before"
            :alt="`Antes: ${alt}`"
            class="absolute inset-0 size-full object-cover"
            loading="lazy"
            draggable="false"
        />

        <div
            class="absolute inset-0"
            :style="{ clipPath: `inset(0 0 0 ${position}%)` }"
        >
            <img
                :src="after"
                :alt="`Después: ${alt}`"
                class="size-full object-cover"
                loading="lazy"
                draggable="false"
            />
        </div>

        <div
            class="pointer-events-none absolute inset-y-0 z-10 w-0.5 -translate-x-1/2 bg-white/90 shadow-[0_0_14px_rgba(0,19,48,0.5)]"
            :style="{ left: `${position}%` }"
            aria-hidden="true"
        ></div>

        <span
            class="pointer-events-none absolute top-1/2 z-10 flex size-9 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full border border-white/70 bg-white/95 text-luda-navy shadow-lg"
            :style="{ left: `${position}%` }"
            aria-hidden="true"
        >
            <MoveHorizontal class="size-4" />
        </span>

        <span
            class="pointer-events-none absolute top-3 left-3 z-10 rounded-full bg-luda-navy/85 px-2.5 py-1 text-[10px] font-semibold tracking-[0.14em] text-white uppercase backdrop-blur"
        >
            Antes
        </span>
        <span
            class="pointer-events-none absolute top-3 right-3 z-10 rounded-full bg-luda-blue/90 px-2.5 py-1 text-[10px] font-semibold tracking-[0.14em] text-white uppercase backdrop-blur"
        >
            Después
        </span>

        <input
            v-model.number="position"
            type="range"
            min="0"
            max="100"
            step="1"
            class="absolute inset-0 z-20 h-full w-full cursor-ew-resize touch-pan-y opacity-0"
            :aria-label="`Comparar antes y después: ${alt}`"
        />
    </div>
</template>
