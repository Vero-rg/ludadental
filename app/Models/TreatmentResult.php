<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property string $titulo
 * @property string $descripcion
 * @property string|null $servicio
 * @property string $imagen_antes
 * @property string $imagen_despues
 * @property bool $visible
 * @property int $orden
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'titulo',
    'descripcion',
    'servicio',
    'imagen_antes',
    'imagen_despues',
    'visible',
    'orden',
])]
class TreatmentResult extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'visible' => 'boolean',
            'orden' => 'integer',
        ];
    }

    /**
     * Resultados publicados para la galería de la landing.
     *
     * @return list<array<string, mixed>>
     */
    public static function gallery(): array
    {
        $results = [];

        $records = self::query()
            ->where('visible', true)
            ->orderBy('orden')
            ->orderByDesc('created_at')
            ->get();

        foreach ($records as $result) {
            $results[] = $result->toGalleryArray();
        }

        return $results;
    }

    /**
     * @return array<string, mixed>
     */
    public function toGalleryArray(): array
    {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'servicio' => $this->servicio,
            'imagen_antes' => Storage::disk('public')->url($this->imagen_antes),
            'imagen_despues' => Storage::disk('public')->url($this->imagen_despues),
            'visible' => $this->visible,
            'orden' => $this->orden,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
