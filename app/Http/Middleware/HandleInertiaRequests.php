<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => function () use ($request) {
                $user = $request->user();

                // Obtener los módulos del usuario (ajusta según tu método)
                $modulos = $user ? $user->modulos() : collect();

                // Transformar la colección para Inertia
                $menuItems = $modulos->map(function ($modulo) {
                    return [
                        'label' => $modulo->label ?? \Illuminate\Support\Str::title(str_replace('_', ' ', $modulo->name)),
                        'href'  => '/' . ($modulo->slug ?? $modulo->name),
                        'icon'  => $modulo->icon ?? 'default-icon',
                    ];
                })->values()->all();

                return [
                    'user' => $user ? [
                        'id' => $user->id,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'email' => $user->email,
                        'owner' => $user->owner,
                    ] : null,
                    'menuItems' => $menuItems,
                ];
            },
            'flash' => function () use ($request) {
                return [
                    'success' => $request->session()->get('success'),
                    'error' => $request->session()->get('error'),
                    'qrResult' => fn () => $request->session()->get('qrResult'),
                ];
            },
        ]);
    }
}
