<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TreatmentResultRequest;
use App\Models\TreatmentResult;
use App\Support\AppointmentSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class TreatmentResultController extends Controller
{
    /**
     * Galería de resultados administrable.
     */
    public function index(): Response
    {
        $results = [];

        $records = TreatmentResult::query()
            ->orderBy('orden')
            ->orderByDesc('created_at')
            ->get();

        foreach ($records as $result) {
            $results[] = $result->toGalleryArray();
        }

        return Inertia::render('Admin/Results/Index', [
            'results' => $results,
            'services' => AppointmentSchedule::SERVICES,
        ]);
    }

    /**
     * Publica un nuevo antes y después.
     */
    public function store(TreatmentResultRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $before = $request->file('imagen_antes');
        $after = $request->file('imagen_despues');

        if (! $before instanceof UploadedFile || ! $after instanceof UploadedFile) {
            throw ValidationException::withMessages([
                'imagen_antes' => 'Adjunta las dos imágenes del resultado.',
            ]);
        }

        $servicio = $data['servicio'] ?? null;

        TreatmentResult::create([
            'titulo' => (string) $data['titulo'],
            'descripcion' => (string) $data['descripcion'],
            'servicio' => is_string($servicio) ? $servicio : null,
            'imagen_antes' => $before->store('resultados', 'public'),
            'imagen_despues' => $after->store('resultados', 'public'),
            'visible' => $request->boolean('visible'),
            'orden' => (int) ($data['orden'] ?? 0),
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Resultado publicado correctamente.',
        ]);

        return back();
    }

    /**
     * Actualiza la información o reemplaza las imágenes de un resultado.
     */
    public function update(TreatmentResultRequest $request, TreatmentResult $result): RedirectResponse
    {
        $data = $request->validated();
        $servicio = $data['servicio'] ?? null;

        $payload = [
            'titulo' => (string) $data['titulo'],
            'descripcion' => (string) $data['descripcion'],
            'servicio' => is_string($servicio) ? $servicio : null,
            'visible' => $request->boolean('visible'),
            'orden' => (int) ($data['orden'] ?? 0),
        ];

        foreach (['imagen_antes', 'imagen_despues'] as $field) {
            $file = $request->file($field);

            if (! $file instanceof UploadedFile) {
                continue;
            }

            $previous = (string) $result->getAttribute($field);

            if ($previous !== '') {
                Storage::disk('public')->delete($previous);
            }

            $payload[$field] = $file->store('resultados', 'public');
        }

        $result->update($payload);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Resultado actualizado correctamente.',
        ]);

        return back();
    }

    /**
     * Muestra u oculta un resultado en la landing.
     */
    public function toggleVisibility(Request $request, TreatmentResult $result): RedirectResponse
    {
        $request->validate([
            'visible' => ['required', 'boolean'],
        ]);

        $result->update([
            'visible' => $request->boolean('visible'),
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => $result->visible
                ? 'El resultado ya se muestra en el sitio.'
                : 'El resultado se ocultó del sitio.',
        ]);

        return back();
    }

    /**
     * Elimina un resultado y sus imágenes.
     */
    public function destroy(TreatmentResult $result): RedirectResponse
    {
        Storage::disk('public')->delete([
            $result->imagen_antes,
            $result->imagen_despues,
        ]);

        $result->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Resultado eliminado correctamente.',
        ]);

        return back();
    }
}
