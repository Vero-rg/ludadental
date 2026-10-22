<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { KeyRound } from '@lucide/vue';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { update } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Restablecer contraseña',
        description: 'Ingresa y confirma tu nueva contraseña.',
    },
});

const props = defineProps<{
    token: string;
    email: string;
    passwordRules: string;
}>();

const inputEmail = ref(props.email);

const fieldClass =
    'h-11 rounded-xl border-luda-navy/10 bg-luda-mist/50 px-3.5 text-sm text-luda-ink shadow-none placeholder:text-luda-ink/30 focus-visible:border-luda-sky focus-visible:ring-luda-sky/40';
</script>

<template>
    <Head title="Restablecer contraseña" />

    <Form
        v-bind="update.form()"
        :transform="(data) => ({ ...data, token, email })"
        :reset-on-success="['password', 'password_confirmation']"
        v-slot="{ errors, processing }"
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
                    autocomplete="email"
                    v-model="inputEmail"
                    class="mt-1 block w-full"
                    :class="fieldClass"
                    readonly
                />
                <InputError :message="errors.email" class="mt-2" />
            </div>

            <div class="grid gap-2">
                <Label
                    for="password"
                    class="text-xs font-semibold text-luda-navy"
                >
                    Nueva contraseña
                </Label>
                <PasswordInput
                    id="password"
                    name="password"
                    autocomplete="new-password"
                    class="mt-1 block w-full"
                    :class="fieldClass"
                    autofocus
                    placeholder="Nueva contraseña"
                    :passwordrules="passwordRules"
                />
                <InputError :message="errors.password" />
            </div>

            <div class="grid gap-2">
                <Label
                    for="password_confirmation"
                    class="text-xs font-semibold text-luda-navy"
                >
                    Confirmar contraseña
                </Label>
                <PasswordInput
                    id="password_confirmation"
                    name="password_confirmation"
                    autocomplete="new-password"
                    class="mt-1 block w-full"
                    :class="fieldClass"
                    placeholder="Confirma tu contraseña"
                    :passwordrules="passwordRules"
                />
                <InputError :message="errors.password_confirmation" />
            </div>

            <Button
                type="submit"
                class="mt-1 h-11 w-full rounded-xl bg-luda-navy text-sm font-semibold text-white shadow-lg shadow-luda-navy/20 transition hover:bg-luda-blue disabled:opacity-60"
                :disabled="processing"
                data-test="reset-password-button"
            >
                <Spinner v-if="processing" />
                <KeyRound v-else class="size-4" />
                Restablecer contraseña
            </Button>
        </div>
    </Form>
</template>
