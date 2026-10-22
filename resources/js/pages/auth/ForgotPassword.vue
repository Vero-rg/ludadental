<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Mail } from '@lucide/vue';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { email } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Recuperar contraseña',
        description:
            'Ingresa tu correo y te enviaremos un enlace para restablecerla.',
    },
});

defineProps<{
    status?: string;
}>();

const fieldClass =
    'h-11 rounded-xl border-luda-navy/10 bg-luda-mist/50 px-3.5 text-sm text-luda-ink shadow-none placeholder:text-luda-ink/30 focus-visible:border-luda-sky focus-visible:ring-luda-sky/40';
</script>

<template>
    <Head title="Recuperar contraseña" />

    <div
        v-if="status"
        class="mb-5 rounded-xl bg-emerald-50 px-4 py-3 text-center text-sm font-medium text-emerald-700 ring-1 ring-emerald-200"
    >
        {{ status }}
    </div>

    <div class="space-y-6">
        <Form v-bind="email.form()" v-slot="{ errors, processing }">
            <div class="grid gap-2">
                <Label for="email" class="text-xs font-semibold text-luda-navy">
                    Correo electrónico
                </Label>
                <Input
                    id="email"
                    type="email"
                    name="email"
                    autocomplete="off"
                    v-focus
                    placeholder="tucorreo@ejemplo.com"
                    :class="fieldClass"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="my-6 flex items-center justify-start">
                <Button
                    class="h-11 w-full rounded-xl bg-luda-navy text-sm font-semibold text-white shadow-lg shadow-luda-navy/20 transition hover:bg-luda-blue disabled:opacity-60"
                    :disabled="processing"
                    data-test="email-password-reset-link-button"
                >
                    <Spinner v-if="processing" />
                    <Mail v-else class="size-4" />
                    Enviar enlace de recuperación
                </Button>
            </div>
        </Form>

        <div class="space-x-1 text-center text-sm text-muted-foreground">
            <span>O vuelve a</span>
            <TextLink :href="login()" class="font-semibold">
                iniciar sesión
            </TextLink>
        </div>
    </div>
</template>
