<script setup lang="ts">
import { computed, onBeforeUnmount, ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import {
    Eye,
    EyeOff,
    ImagePlus,
    Images,
    LoaderCircle,
    Pencil,
    Plus,
    Trash2,
    Upload,
} from '@lucide/vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogDescription,
    DialogScrollContent,
    DialogTitle,
} from '@/components/ui/dialog';
import type { TreatmentResultRecord } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Resultados',
                href: '/admin/resultados',
            },
        ],
    },
});

const props = defineProps<{
    results: TreatmentResultRecord[];
    services: string[];
}>();

const isOpen = ref(false);
const editing = ref<TreatmentResultRecord | null>(null);
const beforePreview = ref<string | null>(null);
const afterPreview = ref<string | null>(null);

const form = useForm({
    titulo: '',
    descripcion: '',
    servicio: '',
    imagen_antes: null as File | null,
    imagen_despues: null as File | null,
    visible: true,
    orden: 0,
});

const visibleCount = computed(
    () => props.results.filter((result) => result.visible).length,
);

const dialogOpen = computed({
    get: () => isOpen.value,
    set: (value: boolean) => {
        if (value) {
            isOpen.value = true;

            return;
        }

        closeDialog();
    },
});

function releasePreview(url: string | null): void {
    if (url?.startsWith('blob:')) {
        URL.revokeObjectURL(url);
    }
}

function clearPreviews(): void {
    releasePreview(beforePreview.value);
    releasePreview(afterPreview.value);
    beforePreview.value = null;
    afterPreview.value = null;
}

function onBeforeChange(event: Event): void {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null;

    form.imagen_antes = file;
    releasePreview(beforePreview.value);
    beforePreview.value = file ? URL.createObjectURL(file) : null;
}

function onAfterChange(event: Event): void {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null;

    form.imagen_despues = file;
    releasePreview(afterPreview.value);
    afterPreview.value = file ? URL.createObjectURL(file) : null;
}

function openCreate(): void {
    editing.value = null;
    form.reset();
    form.clearErrors();
    clearPreviews();
    isOpen.value = true;
}

function openEdit(result: TreatmentResultRecord): void {
    editing.value = result;
    form.clearErrors();
    form.titulo = result.titulo;
    form.descripcion = result.descripcion;
    form.servicio = result.servicio ?? '';
    form.imagen_antes = null;
    form.imagen_despues = null;
    form.visible = result.visible;
    form.orden = result.orden;
    clearPreviews();
    beforePreview.value = result.imagen_antes;
    afterPreview.value = result.imagen_despues;
    isOpen.value = true;
}

function closeDialog(): void {
    isOpen.value = false;
    editing.value = null;
    clearPreviews();
    form.reset();
    form.clearErrors();
}

function submit(): void {
    const result = editing.value;
    const url = result
        ? `/admin/resultados/${result.id}`
        : '/admin/resultados';

    form.post(url, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => closeDialog(),
    });
}

function toggleVisibility(result: TreatmentResultRecord): void {
    router.patch(
        `/admin/resultados/${result.id}/visibilidad`,
        { visible: !result.visible },
        { preserveScroll: true },
    );
}

function destroy(result: TreatmentResultRecord): void {
    const confirmed = window.confirm(
        `¿Eliminar el resultado "${result.titulo}"? Esta acción no se puede deshacer.`,
    );

    if (confirmed) {
        router.delete(`/admin/resultados/${result.id}`, {
            preserveScroll: true,
        });
    }
}

onBeforeUnmount(clearPreviews);

const fieldClass =
    'w-full rounded-xl border border-luda-navy/10 bg-white px-3.5 py-2.5 text-sm text-luda-ink shadow-sm outline-none transition placeholder:text-luda-ink/30 focus:border-luda-sky focus:ring-2 focus:ring-luda-sky/40';
</script>

<template>
    <Head title="Resultados antes y después" />

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
                    Resultados antes y después
                </h1>
                <p class="mt-1.5 max-w-2xl text-sm text-luda-ink/55">
                    Sube las fotos y descripciones que se muestran en la
                    sección de resultados de la página principal.
                </p>
            </div>

            <button
                type="button"
                class="inline-flex w-fit items-center gap-2 rounded-full bg-luda-navy px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-luda-navy/20 transition hover:bg-luda-blue"
                @click="openCreate"
            >
                <Plus class="size-4" />
                Nuevo resultado
            </button>
        </header>

        <div class="flex flex-wrap items-center gap-2 text-xs">
            <span
                class="inline-flex items-center gap-1.5 rounded-full border border-luda-navy/5 bg-white px-3 py-1.5 font-semibold text-luda-ink/60 shadow-sm"
            >
                <Images class="size-3.5 text-luda-blue" />
                {{ results.length }} resultados
            </span>
            <span
                class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200/60 bg-emerald-50 px-3 py-1.5 font-semibold text-emerald-700"
            >
                <Eye class="size-3.5" />
                {{ visibleCount }} visibles
            </span>
            <span
                class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1.5 font-semibold text-luda-ink/55"
            >
                <EyeOff class="size-3.5" />
                {{ results.length - visibleCount }} ocultos
            </span>
        </div>

        <section
            v-if="results.length === 0"
            class="flex flex-col items-center justify-center gap-4 rounded-2xl border border-luda-navy/5 bg-white px-6 py-16 text-center shadow-[0_24px_70px_-55px_rgba(0,19,48,0.55)]"
        >
            <span
                class="flex size-14 items-center justify-center rounded-2xl bg-luda-mist text-luda-blue"
            >
                <ImagePlus class="size-7" />
            </span>
            <div>
                <p class="text-sm font-semibold text-luda-navy">
                    Aún no hay resultados publicados
                </p>
                <p class="mt-1 max-w-md text-xs leading-relaxed text-luda-ink/50">
                    Sube tu primer antes y después para mostrarlo en la landing.
                    Necesitas dos imágenes y una descripción breve.
                </p>
            </div>
            <button
                type="button"
                class="inline-flex items-center gap-2 rounded-full bg-luda-navy px-4 py-2 text-xs font-semibold text-white transition hover:bg-luda-blue"
                @click="openCreate"
            >
                <Plus class="size-3.5" />
                Subir el primer resultado
            </button>
        </section>

        <div v-else class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <article
                v-for="result in results"
                :key="result.id"
                class="overflow-hidden rounded-2xl border border-luda-navy/5 bg-white shadow-[0_18px_50px_-42px_rgba(0,19,48,0.55)]"
            >
                <div class="grid grid-cols-2 gap-px bg-luda-navy/5">
                    <div class="relative aspect-[4/3] overflow-hidden bg-luda-mist">
                        <img
                            :src="result.imagen_antes"
                            :alt="`Antes: ${result.titulo}`"
                            class="size-full object-cover"
                            loading="lazy"
                        />
                        <span
                            class="absolute top-2 left-2 rounded-full bg-luda-navy/85 px-2 py-0.5 text-[9px] font-semibold tracking-[0.12em] text-white uppercase backdrop-blur"
                        >
                            Antes
                        </span>
                    </div>
                    <div class="relative aspect-[4/3] overflow-hidden bg-luda-mist">
                        <img
                            :src="result.imagen_despues"
                            :alt="`Después: ${result.titulo}`"
                            class="size-full object-cover"
                            loading="lazy"
                        />
                        <span
                            class="absolute top-2 right-2 rounded-full bg-luda-blue/90 px-2 py-0.5 text-[9px] font-semibold tracking-[0.12em] text-white uppercase backdrop-blur"
                        >
                            Después
                        </span>
                    </div>
                </div>

                <div class="p-4">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p
                                v-if="result.servicio"
                                class="text-[10px] font-semibold tracking-[0.14em] text-luda-blue uppercase"
                            >
                                {{ result.servicio }}
                            </p>
                            <h2
                                class="mt-1 truncate text-sm font-semibold text-luda-navy"
                                :title="result.titulo"
                            >
                                {{ result.titulo }}
                            </h2>
                        </div>
                        <span
                            :class="[
                                'shrink-0 rounded-full px-2 py-0.5 text-[10px] font-semibold ring-1 ring-inset',
                                result.visible
                                    ? 'bg-emerald-50 text-emerald-700 ring-emerald-200'
                                    : 'bg-slate-100 text-slate-500 ring-slate-200',
                            ]"
                        >
                            {{ result.visible ? 'Visible' : 'Oculto' }}
                        </span>
                    </div>

                    <p
                        class="mt-2 line-clamp-2 text-xs leading-relaxed text-luda-ink/55"
                        :title="result.descripcion"
                    >
                        {{ result.descripcion }}
                    </p>

                    <div class="mt-4 flex items-center justify-between gap-2">
                        <button
                            type="button"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-luda-navy/10 px-2.5 py-1.5 text-[11px] font-semibold text-luda-ink/60 transition hover:bg-luda-mist hover:text-luda-navy"
                            @click="toggleVisibility(result)"
                        >
                            <EyeOff v-if="result.visible" class="size-3.5" />
                            <Eye v-else class="size-3.5" />
                            {{ result.visible ? 'Ocultar' : 'Publicar' }}
                        </button>

                        <div class="flex items-center gap-1.5">
                            <button
                                type="button"
                                class="flex size-8 items-center justify-center rounded-lg border border-luda-navy/10 text-luda-ink/55 transition hover:border-luda-sky hover:bg-luda-mist hover:text-luda-blue"
                                title="Editar resultado"
                                aria-label="Editar resultado"
                                @click="openEdit(result)"
                            >
                                <Pencil class="size-4" />
                            </button>
                            <button
                                type="button"
                                class="flex size-8 items-center justify-center rounded-lg border border-luda-navy/10 text-luda-ink/55 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600"
                                title="Eliminar resultado"
                                aria-label="Eliminar resultado"
                                @click="destroy(result)"
                            >
                                <Trash2 class="size-4" />
                            </button>
                        </div>
                    </div>
                </div>
            </article>
        </div>

        <Dialog v-model:open="dialogOpen">
            <DialogScrollContent class="gap-0 border-0 bg-white p-0 sm:max-w-2xl">
                <div class="border-b border-luda-navy/5 px-6 pt-6 pb-5">
                    <DialogTitle class="text-lg font-semibold text-luda-navy">
                        {{ editing ? 'Editar resultado' : 'Nuevo resultado' }}
                    </DialogTitle>
                    <DialogDescription class="mt-1 text-sm text-luda-ink/55">
                        Sube el antes y el después, y agrega una descripción
                        breve del tratamiento.
                    </DialogDescription>
                </div>

                <form class="space-y-5 px-6 py-5" @submit.prevent="submit">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="grid gap-2 sm:col-span-2">
                            <label
                                for="resultado_titulo"
                                class="text-xs font-semibold text-luda-navy"
                            >
                                Título del resultado
                            </label>
                            <input
                                id="resultado_titulo"
                                v-model="form.titulo"
                                type="text"
                                maxlength="120"
                                placeholder="Ej. Rehabilitación con carillas"
                                :class="[
                                    fieldClass,
                                    form.errors.titulo
                                        ? 'border-red-300 focus:border-red-400 focus:ring-red-200'
                                        : '',
                                ]"
                            />
                            <InputError :message="form.errors.titulo" />
                        </div>

                        <div class="grid gap-2">
                            <label
                                for="resultado_servicio"
                                class="text-xs font-semibold text-luda-navy"
                            >
                                Servicio
                                <span class="font-normal text-luda-ink/40">
                                    (opcional)
                                </span>
                            </label>
                            <select
                                id="resultado_servicio"
                                v-model="form.servicio"
                                :class="[
                                    fieldClass,
                                    'appearance-none',
                                    form.errors.servicio
                                        ? 'border-red-300 focus:border-red-400 focus:ring-red-200'
                                        : '',
                                ]"
                            >
                                <option value="">Sin servicio específico</option>
                                <option
                                    v-for="service in services"
                                    :key="service"
                                    :value="service"
                                >
                                    {{ service }}
                                </option>
                            </select>
                            <InputError :message="form.errors.servicio" />
                        </div>

                        <div class="grid gap-2">
                            <label
                                for="resultado_orden"
                                class="text-xs font-semibold text-luda-navy"
                            >
                                Orden
                            </label>
                            <input
                                id="resultado_orden"
                                v-model.number="form.orden"
                                type="number"
                                min="0"
                                max="999"
                                :class="[
                                    fieldClass,
                                    form.errors.orden
                                        ? 'border-red-300 focus:border-red-400 focus:ring-red-200'
                                        : '',
                                ]"
                            />
                            <p class="text-[10px] text-luda-ink/45">
                                Menor número aparece primero.
                            </p>
                            <InputError :message="form.errors.orden" />
                        </div>

                        <div class="grid gap-2 sm:col-span-2">
                            <label
                                for="resultado_descripcion"
                                class="text-xs font-semibold text-luda-navy"
                            >
                                Descripción breve
                            </label>
                            <textarea
                                id="resultado_descripcion"
                                v-model="form.descripcion"
                                rows="3"
                                maxlength="600"
                                placeholder="Ej. Paciente que recuperó la armonía de su sonrisa en 4 sesiones."
                                :class="[
                                    fieldClass,
                                    'resize-none',
                                    form.errors.descripcion
                                        ? 'border-red-300 focus:border-red-400 focus:ring-red-200'
                                        : '',
                                ]"
                            ></textarea>
                            <div class="flex items-start justify-between gap-3">
                                <InputError :message="form.errors.descripcion" />
                                <span class="shrink-0 text-[10px] text-luda-ink/40">
                                    {{ form.descripcion.length }}/600
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="grid gap-2">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs font-semibold text-luda-navy">
                                    Foto del antes
                                </span>
                                <span
                                    v-if="beforePreview"
                                    class="flex items-center gap-1 text-[10px] font-semibold text-luda-blue"
                                >
                                    <Upload class="size-3" />
                                    Cambiar
                                </span>
                            </div>
                            <label
                                :class="[
                                    'group relative flex aspect-[4/3] cursor-pointer items-center justify-center overflow-hidden rounded-xl border-2 border-dashed bg-luda-mist/60 transition hover:border-luda-sky',
                                    form.errors.imagen_antes
                                        ? 'border-red-300'
                                        : 'border-luda-navy/10',
                                ]"
                            >
                                <img
                                    v-if="beforePreview"
                                    :src="beforePreview"
                                    alt="Vista previa de la imagen del antes"
                                    class="absolute inset-0 size-full object-cover"
                                />
                                <span
                                    v-else
                                    class="flex flex-col items-center gap-1.5 px-4 text-center"
                                >
                                    <ImagePlus class="size-7 text-luda-blue" />
                                    <span class="text-xs font-semibold text-luda-navy">
                                        Selecciona una imagen
                                    </span>
                                    <span class="text-[10px] text-luda-ink/45">
                                        JPG, PNG o WEBP · máx. 2 MB
                                    </span>
                                </span>
                                <input
                                    type="file"
                                    accept="image/jpeg,image/png,image/webp"
                                    class="sr-only"
                                    @change="onBeforeChange"
                                />
                            </label>
                            <InputError :message="form.errors.imagen_antes" />
                        </div>

                        <div class="grid gap-2">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs font-semibold text-luda-navy">
                                    Foto del después
                                </span>
                                <span
                                    v-if="afterPreview"
                                    class="flex items-center gap-1 text-[10px] font-semibold text-luda-blue"
                                >
                                    <Upload class="size-3" />
                                    Cambiar
                                </span>
                            </div>
                            <label
                                :class="[
                                    'group relative flex aspect-[4/3] cursor-pointer items-center justify-center overflow-hidden rounded-xl border-2 border-dashed bg-luda-mist/60 transition hover:border-luda-sky',
                                    form.errors.imagen_despues
                                        ? 'border-red-300'
                                        : 'border-luda-navy/10',
                                ]"
                            >
                                <img
                                    v-if="afterPreview"
                                    :src="afterPreview"
                                    alt="Vista previa de la imagen del después"
                                    class="absolute inset-0 size-full object-cover"
                                />
                                <span
                                    v-else
                                    class="flex flex-col items-center gap-1.5 px-4 text-center"
                                >
                                    <ImagePlus class="size-7 text-luda-blue" />
                                    <span class="text-xs font-semibold text-luda-navy">
                                        Selecciona una imagen
                                    </span>
                                    <span class="text-[10px] text-luda-ink/45">
                                        JPG, PNG o WEBP · máx. 2 MB
                                    </span>
                                </span>
                                <input
                                    type="file"
                                    accept="image/jpeg,image/png,image/webp"
                                    class="sr-only"
                                    @change="onAfterChange"
                                />
                            </label>
                            <InputError :message="form.errors.imagen_despues" />
                        </div>
                    </div>

                    <label
                        class="flex cursor-pointer items-start gap-3 rounded-xl border border-luda-navy/10 bg-luda-mist/50 px-4 py-3"
                    >
                        <input
                            v-model="form.visible"
                            type="checkbox"
                            class="mt-0.5 size-4 rounded border-luda-navy/20 text-luda-blue focus:ring-luda-sky"
                        />
                        <span>
                            <span class="block text-xs font-semibold text-luda-navy">
                                Mostrar en el sitio
                            </span>
                            <span class="block text-[11px] leading-relaxed text-luda-ink/50">
                                Si lo desactivas, el resultado se guarda pero no
                                aparece en la landing.
                            </span>
                        </span>
                    </label>

                    <div
                        class="flex flex-col-reverse gap-2 border-t border-luda-navy/5 pt-4 sm:flex-row sm:justify-end"
                    >
                        <Button
                            type="button"
                            variant="outline"
                            class="border-luda-navy/10 text-luda-navy hover:bg-luda-mist"
                            @click="closeDialog"
                        >
                            Cancelar
                        </Button>
                        <Button
                            type="submit"
                            :disabled="form.processing"
                            class="bg-luda-navy text-white hover:bg-luda-blue disabled:opacity-50"
                        >
                            <LoaderCircle
                                v-if="form.processing"
                                class="size-4 animate-spin"
                            />
                            <Upload v-else class="size-4" />
                            {{ editing ? 'Guardar cambios' : 'Publicar resultado' }}
                        </Button>
                    </div>
                </form>
            </DialogScrollContent>
        </Dialog>
    </div>
</template>
