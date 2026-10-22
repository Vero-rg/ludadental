<?php

namespace App\Support;

final class ClinicBranding
{
    /**
     * @return array{name: string, header_logo: string, footer_logo: string}
     */
    public static function toArray(): array
    {
        return [
            'name' => (string) config('clinic.name', 'LuDa Dental'),
            'header_logo' => self::logoUrl('clinic.logos.header'),
            'footer_logo' => self::logoUrl('clinic.logos.footer'),
        ];
    }

    private static function logoUrl(string $configKey): string
    {
        $path = ltrim(trim((string) config($configKey, '')), '/');

        if ($path === '') {
            return '';
        }

        if (preg_match('/\.(svg|png|webp|jpe?g|avif)$/i', $path) === 1) {
            return is_file(public_path($path)) ? asset($path) : '';
        }

        foreach (['svg', 'png', 'webp', 'jpg', 'jpeg', 'avif'] as $extension) {
            $candidate = "{$path}.{$extension}";

            if (is_file(public_path($candidate))) {
                return asset($candidate);
            }
        }

        return '';
    }
}
