<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tramite;
use App\Models\SolicitudTramite;
use App\Models\Unidad;
use App\Models\Responsable;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Documento;
use App\Models\Workflow;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        if (empty($query) || strlen($query) < 2) {
            if ($request->expectsJson()) {
                return response()->json(['results' => []]);
            }
            return back();
        }

        // Normalizar la consulta para manejar acentos
        $normalizedQuery = $this->normalizeString($query);
        $queryWords = array_filter(explode(' ', $normalizedQuery));

        $results = [];

        // Buscar en Usuarios
        $users = User::where(function($q) use ($query, $queryWords) {
                // Búsqueda original con acentos
                $q->where('first_name', 'ilike', "%{$query}%")
                  ->orWhere('last_name', 'ilike', "%{$query}%")
                  ->orWhereRaw("concat(first_name, ' ', last_name) ilike ?", ["%{$query}%"])
                  ->orWhere('email', 'ilike', "%{$query}%");
                
                // Búsqueda por palabras individuales
                foreach ($queryWords as $word) {
                    if (strlen($word) >= 2) {
                        $q->orWhere('first_name', 'ilike', "%{$word}%")
                          ->orWhere('last_name', 'ilike', "%{$word}%")
                          ->orWhere('email', 'ilike', "%{$word}%"); // Buscar en correo también
                    }
                }
            })
            ->limit(5)
            ->get()
            ->filter(function($user) use ($normalizedQuery) {
                // Filtro adicional en PHP para manejar acentos
                $userName = $this->normalizeString($user->first_name . ' ' . $user->last_name);
                $userEmail = $this->normalizeString($user->email);
                return strpos($userName, $normalizedQuery) !== false || strpos($userEmail, $normalizedQuery) !== false;
            })
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'title' => $user->first_name . ' ' . $user->last_name,
                    'subtitle' => $user->email,
                    'type' => 'user',
                    'icon' => 'user',
                    'url' => "/users/{$user->id}/edit",
                    'description' => "Usuario del sistema",
                    'origin' => 'Usuarios'
                ];
            });

        $results = array_merge($results, $users->toArray());

        // Buscar en Trámites
        $tramites = Tramite::where(function($q) use ($query, $queryWords) {
                $q->where('nombre', 'ilike', "%{$query}%")
                  ->orWhereHas('workflow', function($wq) use ($query) {
                      $wq->where('nombre', 'ilike', "%{$query}%")
                         ->orWhere('descripcion', 'ilike', "%{$query}%");
                  });
                
                foreach ($queryWords as $word) {
                    if (strlen($word) >= 2) {
                        $q->orWhere('nombre', 'ilike', "%{$word}%");
                    }
                }
            })
            ->with('workflow')
            ->limit(5)
            ->get()
            ->filter(function($tramite) use ($normalizedQuery) {
                $tramiteName = $this->normalizeString($tramite->nombre);
                return strpos($tramiteName, $normalizedQuery) !== false;
            })
            ->map(function ($tramite) {
                return [
                    'id' => $tramite->id,
                    'title' => $tramite->nombre,
                    'subtitle' => $tramite->workflow ? $tramite->workflow->nombre : 'Sin workflow',
                    'type' => 'tramite',
                    'icon' => 'document',
                    'url' => "/tramites/{$tramite->id}",
                    'description' => "Trámite del sistema",
                    'origin' => 'Trámites'
                ];
            });

        $results = array_merge($results, $tramites->toArray());

        // Buscar en Solicitudes de Trámite
        $solicitudes = SolicitudTramite::where(function($q) use ($query, $queryWords) {
                $q->where('nombre', 'ilike', "%{$query}%")
                  ->orWhere('estado_actual', 'ilike', "%{$query}%")
                  ->orWhereHas('tramite', function($tq) use ($query) {
                      $tq->where('nombre', 'ilike', "%{$query}%");
                  })
                  ->orWhereHas('usuario', function($uq) use ($query) {
                      $uq->where('first_name', 'ilike', "%{$query}%")
                         ->orWhere('last_name', 'ilike', "%{$query}%")
                         ->orWhereRaw("concat(first_name, ' ', last_name) ilike ?", ["%{$query}%"]);
                  });
                
                foreach ($queryWords as $word) {
                    if (strlen($word) >= 2) {
                        $q->orWhere('nombre', 'ilike', "%{$word}%")
                          ->orWhereHas('usuario', function($uq) use ($word) {
                              $uq->where('first_name', 'ilike', "%{$word}%")
                                 ->orWhere('last_name', 'ilike', "%{$word}%");
                          });
                    }
                }
            })
            ->with(['tramite', 'usuario'])
            ->limit(5)
            ->get()
            ->filter(function($solicitud) use ($normalizedQuery) {
                $solicitudName = $this->normalizeString($solicitud->nombre);
                $usuarioName = $solicitud->usuario ? $this->normalizeString($solicitud->usuario->first_name . ' ' . $solicitud->usuario->last_name) : '';
                return strpos($solicitudName, $normalizedQuery) !== false || strpos($usuarioName, $normalizedQuery) !== false;
            })
            ->map(function ($solicitud) {
                return [
                    'id' => $solicitud->id,
                    'title' => "Solicitud #{$solicitud->id}",
                    'subtitle' => $solicitud->tramite ? $solicitud->tramite->nombre : 'Sin trámite',
                    'type' => 'solicitud',
                    'icon' => 'clipboard',
                    'url' => "/solicitudes/{$solicitud->id}",
                    'description' => "Solicitante: " . ($solicitud->usuario ? $solicitud->usuario->name : 'N/A'),
                    'origin' => 'Solicitudes'
                ];
            });

        $results = array_merge($results, $solicitudes->toArray());

        // Buscar en Unidades
        $unidades = Unidad::where(function($q) use ($query, $queryWords) {
                $q->where('name', 'ilike', "%{$query}%")
                  ->orWhere('phone', 'ilike', "%{$query}%")
                  ->orWhere('city', 'ilike', "%{$query}%");
                
                foreach ($queryWords as $word) {
                    if (strlen($word) >= 2) {
                        $q->orWhere('name', 'ilike', "%{$word}%")
                          ->orWhere('city', 'ilike', "%{$word}%");
                    }
                }
            })
            ->limit(5)
            ->get()
            ->filter(function($unidad) use ($normalizedQuery) {
                $unidadName = $this->normalizeString($unidad->name);
                $unidadCity = $this->normalizeString($unidad->city ?? '');
                return strpos($unidadName, $normalizedQuery) !== false || strpos($unidadCity, $normalizedQuery) !== false;
            })
            ->map(function ($unidad) {
                return [
                    'id' => $unidad->id,
                    'title' => $unidad->name,
                    'subtitle' => 'Unidad',
                    'type' => 'unidad',
                    'icon' => 'building',
                    'url' => "/unidades/{$unidad->id}/edit",
                    'description' => $unidad->city ? "Ciudad: {$unidad->city}" : 'Sin ciudad',
                    'origin' => 'Unidades'
                ];
            });

        $results = array_merge($results, $unidades->toArray());

        // Buscar en Responsables
        $responsables = Responsable::where(function($q) use ($query, $queryWords) {
                $q->where('first_name', 'ilike', "%{$query}%")
                  ->orWhere('last_name', 'ilike', "%{$query}%")
                  ->orWhereRaw("concat(first_name, ' ', last_name) ilike ?", ["%{$query}%"])
                  ->orWhere('email', 'ilike', "%{$query}%");
                
                foreach ($queryWords as $word) {
                    if (strlen($word) >= 2) {
                        $q->orWhere('first_name', 'ilike', "%{$word}%")
                          ->orWhere('last_name', 'ilike', "%{$word}%");
                    }
                }
            })
            ->with('unidad')
            ->limit(5)
            ->get()
            ->filter(function($responsable) use ($normalizedQuery) {
                $responsableName = $this->normalizeString($responsable->first_name . ' ' . $responsable->last_name);
                return strpos($responsableName, $normalizedQuery) !== false;
            })
            ->map(function ($responsable) {
                return [
                    'id' => $responsable->id,
                    'title' => $responsable->name,
                    'subtitle' => $responsable->city ? $responsable->city : 'Sin ciudad',
                    'type' => 'responsable',
                    'icon' => 'user-group',
                    'url' => "/responsables/{$responsable->id}/edit",
                    'description' => $responsable->unidad ? "Unidad: {$responsable->unidad->name}" : 'Sin unidad',
                    'origin' => 'Responsables'
                ];
            });

        $results = array_merge($results, $responsables->toArray());

        // Buscar en Roles
        $roles = Role::where(function($q) use ($query, $queryWords) {
                $q->where('name', 'ilike', "%{$query}%");
                
                foreach ($queryWords as $word) {
                    if (strlen($word) >= 2) {
                        $q->orWhere('name', 'ilike', "%{$word}%");
                    }
                }
            })
            ->limit(5)
            ->get()
            ->filter(function($role) use ($normalizedQuery) {
                $roleName = $this->normalizeString($role->name);
                return strpos($roleName, $normalizedQuery) !== false;
            })
            ->map(function ($role) {
                return [
                    'id' => $role->id,
                    'title' => $role->name,
                    'subtitle' => 'Rol',
                    'type' => 'role',
                    'icon' => 'shield',
                    'url' => "/roles/{$role->id}/edit",
                    'description' => "Permisos: " . $role->permissions->count(),
                    'origin' => 'Roles'
                ];
            });

        $results = array_merge($results, $roles->toArray());

        // Buscar en Workflows
        $workflows = Workflow::where(function($q) use ($query, $queryWords) {
                $q->where('nombre', 'ilike', "%{$query}%")
                  ->orWhere('descripcion', 'ilike', "%{$query}%");
                
                foreach ($queryWords as $word) {
                    if (strlen($word) >= 2) {
                        $q->orWhere('nombre', 'ilike', "%{$word}%");
                    }
                }
            })
            ->limit(5)
            ->get()
            ->filter(function($workflow) use ($normalizedQuery) {
                $workflowName = $this->normalizeString($workflow->nombre);
                return strpos($workflowName, $normalizedQuery) !== false;
            })
            ->map(function ($workflow) {
                return [
                    'id' => $workflow->id,
                    'title' => $workflow->nombre,
                    'subtitle' => 'Workflow',
                    'type' => 'workflow',
                    'icon' => 'flow',
                    'url' => "/workflows/{$workflow->id}",
                    'description' => substr($workflow->descripcion, 0, 100) . '...',
                    'origin' => 'Workflows'
                ];
            });

        $results = array_merge($results, $workflows->toArray());

        // Buscar en Documentos
        $documentos = Documento::where(function($q) use ($query, $queryWords) {
                $q->where('nombre', 'ilike', "%{$query}%");
                
                foreach ($queryWords as $word) {
                    if (strlen($word) >= 2) {
                        $q->orWhere('nombre', 'ilike', "%{$word}%");
                    }
                }
            })
            ->limit(5)
            ->get()
            ->filter(function($documento) use ($normalizedQuery) {
                $documentoName = $this->normalizeString($documento->nombre);
                return strpos($documentoName, $normalizedQuery) !== false;
            })
            ->map(function ($documento) {
                return [
                    'id' => $documento->id,
                    'title' => $documento->nombre,
                    'subtitle' => 'Documento',
                    'type' => 'documento',
                    'icon' => 'document',
                    'url' => "/documentos/{$documento->id}",
                    'description' => "Archivo: " . basename($documento->path),
                    'origin' => 'Documentos'
                ];
            });

        $results = array_merge($results, $documentos->toArray());

        // Ordenar resultados por relevancia (usuarios primero, luego trámites, etc.)
        usort($results, function ($a, $b) {
            $priority = [
                'user' => 1,
                'tramite' => 2,
                'solicitud' => 3,
                'unidad' => 4,
                'responsable' => 5,
                'role' => 6,
                'workflow' => 7,
                'documento' => 8
            ];
            
            return $priority[$a['type']] <=> $priority[$b['type']];
        });

        // Limitar a 20 resultados totales
        $results = array_slice($results, 0, 20);

        // Si es una petición AJAX, devolver JSON
        if ($request->expectsJson()) {
            return response()->json(['results' => $results]);
        }

        // Si es una petición normal, devolver los resultados como parte de la página
        return back()->with('searchResults', $results);
    }

    public function show(Request $request)
    {
        $query = $request->get('q', '');
        
        if (empty($query)) {
            return redirect()->back();
        }

        return Inertia::render('Search/Results', [
            'query' => $query,
            'results' => $this->getSearchResults($query)
        ]);
    }

    private function getSearchResults($query)
    {
        // Normalizar la consulta para manejar acentos
        $normalizedQuery = $this->normalizeString($query);
        $queryWords = array_filter(explode(' ', $normalizedQuery));
        
        $results = [];

        // Usuarios
        $users = User::where(function($q) use ($query, $queryWords) {
                // Búsqueda original con acentos
                $q->where('first_name', 'ilike', "%{$query}%")
                  ->orWhere('last_name', 'ilike', "%{$query}%")
                  ->orWhereRaw("concat(first_name, ' ', last_name) ilike ?", ["%{$query}%"])
                  ->orWhere('email', 'ilike', "%{$query}%");
                
                // Búsqueda por palabras individuales
                foreach ($queryWords as $word) {
                    if (strlen($word) >= 2) {
                        $q->orWhere('first_name', 'ilike', "%{$word}%")
                          ->orWhere('last_name', 'ilike', "%{$word}%")
                          ->orWhere('email', 'ilike', "%{$word}%"); // Buscar en correo también
                    }
                }
            })
            ->limit(10)
            ->get()
            ->filter(function($user) use ($normalizedQuery) {
                // Filtro adicional en PHP para manejar acentos
                $userName = $this->normalizeString($user->first_name . ' ' . $user->last_name);
                $userEmail = $this->normalizeString($user->email);
                return strpos($userName, $normalizedQuery) !== false || strpos($userEmail, $normalizedQuery) !== false;
            });

        if ($users->count() > 0) {
            $results['users'] = [
                'title' => 'Usuarios',
                'icon' => 'user',
                'items' => $users->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'title' => $user->first_name . ' ' . $user->last_name,
                        'subtitle' => $user->email,
                        'url' => "/users/{$user->id}/edit",
                        'description' => "Usuario del sistema",
                        'origin' => 'Usuarios'
                    ];
                })
            ];
        }

        // Trámites
        $tramites = Tramite::where(function($q) use ($query, $queryWords) {
                $q->where('nombre', 'ilike', "%{$query}%")
                  ->orWhereHas('workflow', function($wq) use ($query) {
                      $wq->where('nombre', 'ilike', "%{$query}%")
                         ->orWhere('descripcion', 'ilike', "%{$query}%");
                  });
                
                foreach ($queryWords as $word) {
                    if (strlen($word) >= 2) {
                        $q->orWhere('nombre', 'ilike', "%{$word}%");
                    }
                }
            })
            ->with('workflow')
            ->limit(10)
            ->get()
            ->filter(function($tramite) use ($normalizedQuery) {
                $tramiteName = $this->normalizeString($tramite->nombre);
                return strpos($tramiteName, $normalizedQuery) !== false;
            });

        if ($tramites->count() > 0) {
            $results['tramites'] = [
                'title' => 'Trámites',
                'icon' => 'document',
                'items' => $tramites->map(function ($tramite) {
                    return [
                        'id' => $tramite->id,
                        'title' => $tramite->nombre,
                        'subtitle' => $tramite->workflow ? $tramite->workflow->nombre : 'Sin workflow',
                        'url' => "/tramites/{$tramite->id}",
                        'description' => "Trámite del sistema",
                        'origin' => 'Trámites'
                    ];
                })
            ];
        }

        // Solicitudes
        $solicitudes = SolicitudTramite::where(function($q) use ($query, $queryWords) {
                $q->where('nombre', 'ilike', "%{$query}%")
                  ->orWhere('estado_actual', 'ilike', "%{$query}%")
                  ->orWhereHas('tramite', function($tq) use ($query) {
                      $tq->where('nombre', 'ilike', "%{$query}%");
                  })
                  ->orWhereHas('usuario', function($uq) use ($query) {
                      $uq->where('first_name', 'ilike', "%{$query}%")
                         ->orWhere('last_name', 'ilike', "%{$query}%")
                         ->orWhereRaw("concat(first_name, ' ', last_name) ilike ?", ["%{$query}%"]);
                  });
                
                foreach ($queryWords as $word) {
                    if (strlen($word) >= 2) {
                        $q->orWhere('nombre', 'ilike', "%{$word}%")
                          ->orWhereHas('usuario', function($uq) use ($word) {
                              $uq->where('first_name', 'ilike', "%{$word}%")
                                 ->orWhere('last_name', 'ilike', "%{$word}%");
                          });
                    }
                }
            })
            ->with(['tramite', 'usuario'])
            ->limit(10)
            ->get()
            ->filter(function($solicitud) use ($normalizedQuery) {
                $solicitudName = $this->normalizeString($solicitud->nombre);
                $usuarioName = $solicitud->usuario ? $this->normalizeString($solicitud->usuario->first_name . ' ' . $solicitud->usuario->last_name) : '';
                return strpos($solicitudName, $normalizedQuery) !== false || strpos($usuarioName, $normalizedQuery) !== false;
            });

        if ($solicitudes->count() > 0) {
            $results['solicitudes'] = [
                'title' => 'Solicitudes',
                'icon' => 'clipboard',
                'items' => $solicitudes->map(function ($solicitud) {
                    return [
                        'id' => $solicitud->id,
                        'title' => "Solicitud #{$solicitud->id}",
                        'subtitle' => $solicitud->tramite ? $solicitud->tramite->nombre : 'Sin trámite',
                        'url' => "/solicitudes/{$solicitud->id}",
                        'description' => "Solicitante: " . ($solicitud->usuario ? $solicitud->usuario->name : 'N/A'),
                        'origin' => 'Solicitudes'
                    ];
                })
            ];
        }

        // Unidades
        $unidades = Unidad::where(function($q) use ($query, $queryWords) {
                $q->where('name', 'ilike', "%{$query}%")
                  ->orWhere('phone', 'ilike', "%{$query}%")
                  ->orWhere('city', 'ilike', "%{$query}%");
                
                foreach ($queryWords as $word) {
                    if (strlen($word) >= 2) {
                        $q->orWhere('name', 'ilike', "%{$word}%")
                          ->orWhere('city', 'ilike', "%{$word}%");
                    }
                }
            })
            ->limit(10)
            ->get()
            ->filter(function($unidad) use ($normalizedQuery) {
                $unidadName = $this->normalizeString($unidad->name);
                $unidadCity = $this->normalizeString($unidad->city ?? '');
                return strpos($unidadName, $normalizedQuery) !== false || strpos($unidadCity, $normalizedQuery) !== false;
            });

        if ($unidades->count() > 0) {
            $results['unidades'] = [
                'title' => 'Unidades',
                'icon' => 'building',
                'items' => $unidades->map(function ($unidad) {
                    return [
                        'id' => $unidad->id,
                        'title' => $unidad->name,
                        'subtitle' => 'Unidad',
                        'url' => "/unidades/{$unidad->id}/edit",
                        'description' => $unidad->city ? "Ciudad: {$unidad->city}" : 'Sin ciudad',
                        'origin' => 'Unidades'
                    ];
                })
            ];
        }

        return $results;
    }

    /**
     * Normaliza una cadena removiendo acentos
     */
    private function normalizeString($string)
    {
        $string = strtolower($string);
        
        // Reemplazar caracteres acentuados
        $replacements = [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n',
            'à' => 'a', 'è' => 'e', 'ì' => 'i', 'ò' => 'o', 'ù' => 'u',
            'â' => 'a', 'ê' => 'e', 'î' => 'i', 'ô' => 'o', 'û' => 'u',
            'ã' => 'a', 'õ' => 'o',
            'ä' => 'a', 'ë' => 'e', 'ï' => 'i', 'ö' => 'o', 'ü' => 'u',
            'ç' => 'c'
        ];
        
        return strtr($string, $replacements);
    }

    /**
     * Normaliza una cadena para búsqueda en base de datos
     */
    private function normalizeForSearch($string)
    {
        return $this->normalizeString($string);
    }
} 