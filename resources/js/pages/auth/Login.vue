<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { LogIn } from '@lucide/vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Bienvenido de nuevo',
        description:
            'Ingresa tus credenciales para acceder al panel de LuDa Dental.',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const fieldClass =
    'h-11 rounded-xl border-luda-navy/10 bg-luda-mist/50 px-3.5 text-sm text-luda-ink shadow-none placeholder:text-luda-ink/30 focus-visible:border-luda-sky focus-visible:ring-luda-sky/40';
</script>

<template>
    <Head title="Iniciar sesión" />

    <div
        v-if="status"
        class="mb-5 rounded-xl bg-emerald-50 px-4 py-3 text-center text-sm font-medium text-emerald-700 ring-1 ring-emerald-200"
    >
        {{ status }}
    </div>

    <Form
        v-bind="store.form()"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-6"
    >
        <div class="grid gap-5">
            <div class="grid gap-2">
                <Label for="email" class="text-xs font-semibold text-luda-navy">
                    Correo electrónico
                </Label>
                <Input
                    id="email"
                    type="email"
                    name="email"
                    required
                    v-focus
                    :tabindex="1"
                    autocomplete="email"
                    placeholder="tucorreo@ejemplo.com"
                    :class="fieldClass"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-2">
                <div class="flex items-center justify-between gap-3">
                    <Label
                        for="password"
                        class="text-xs font-semibold text-luda-navy"
                    >
                        Contraseña
                    </Label>
                    <TextLink
                        v-if="canResetPassword"
                        :href="request()"
                        class="text-xs font-semibold"
                        :tabindex="5"
                    >
                        ¿Olvidaste tu contraseña?
                    </TextLink>
                </div>
                <PasswordInput
                    id="password"
                    name="password"
                    required
                    :tabindex="2"
                    autocomplete="current-password"
                    placeholder="Tu contraseña"
                    :class="fieldClass"
                />
                <InputError :message="errors.password" />
            </div>

            <div class="flex items-center justify-between">
                <Label
                    for="remember"
                    class="flex cursor-pointer items-center gap-3 text-sm text-luda-ink/65"
                >
                    <Checkbox
                        id="remember"
                        name="remember"
                        :tabindex="3"
                        class="size-4 rounded border-luda-navy/20"
                    />
                    <span>Recordarme</span>
                </Label>
            </div>

            <Button
                type="submit"
                class="mt-1 h-11 w-full rounded-xl bg-luda-navy text-sm font-semibold text-white shadow-lg shadow-luda-navy/20 transition hover:bg-luda-blue disabled:opacity-60"
                :tabindex="4"
                :disabled="processing"
                data-test="login-button"
            >
                <Spinner v-if="processing" />
                <LogIn v-else class="size-4" />
                Iniciar sesión
            </Button>
        </div>
    </Form>
</template>
