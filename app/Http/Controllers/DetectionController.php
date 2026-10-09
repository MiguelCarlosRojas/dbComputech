<?php

namespace App\Http\Controllers;

use App\Events\DetectionDetected;
use App\Models\DetectionLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DetectionController extends Controller
{
    /**
     * Color definitions for objects.
     */
    public const OBJECT_COLORS = [
        'person' => ['name' => 'Persona', 'color' => '#3B82F6'],
        'cell phone' => ['name' => 'Teléfono Celular', 'color' => '#F59E0B'],
        'laptop' => ['name' => 'Laptop / Computadora', 'color' => '#10B981'],
        'tv' => ['name' => 'Monitor / Televisor', 'color' => '#06B6D4'],
        'bottle' => ['name' => 'Botella de Agua', 'color' => '#8B5CF6'],
        'cup' => ['name' => 'Taza / Vaso', 'color' => '#14B8A6'],
        'book' => ['name' => 'Libro / Cuaderno', 'color' => '#EC4899'],
        'backpack' => ['name' => 'Mochila / Bolso', 'color' => '#6366F1'],
        'handbag' => ['name' => 'Cartera / Bolso', 'color' => '#D946EF'],
        'chair' => ['name' => 'Silla / Asiento', 'color' => '#64748B'],
        'couch' => ['name' => 'Sofá / Mueble', 'color' => '#78716C'],
        'dining table' => ['name' => 'Mesa de Trabajo', 'color' => '#A855F7'],
        'potted plant' => ['name' => 'Planta Decorativa', 'color' => '#22C55E'],
        'mouse' => ['name' => 'Mouse / Ratón', 'color' => '#0EA5E9'],
        'keyboard' => ['name' => 'Teclado', 'color' => '#84CC16'],
        'clock' => ['name' => 'Reloj de Pared', 'color' => '#F97316'],
        'scissors' => ['name' => 'Tijeras', 'color' => '#F43F5E'],
        'remote' => ['name' => 'Control Remoto', 'color' => '#6366F1'],
        'umbrella' => ['name' => 'Paraguas', 'color' => '#0284C7'],
        'tie' => ['name' => 'Corbata', 'color' => '#4F46E5'],
    ];

    /**
     * Color definitions for behaviors & gestures.
     */
    public const BEHAVIOR_COLORS = [
        'talking_phone' => ['name' => 'Hablando por Teléfono / Celular', 'color' => '#FF5722'],
        'working_laptop' => ['name' => 'Trabajando en Computadora', 'color' => '#059669'],
        'drinking' => ['name' => 'Bebiendo / Consumiendo Líquido', 'color' => '#7C3AED'],
        'reading' => ['name' => 'Leyendo Documento / Libro', 'color' => '#DB2777'],
        'hands_up' => ['name' => 'Gesto: Manos Arriba / Alerta', 'color' => '#DC2626'],
        'waving' => ['name' => 'Gesto: Saludando con la Mano', 'color' => '#F59E0B'],
        'face_touch' => ['name' => 'Gesto: Tocándose la Cara / Fatiga', 'color' => '#E11D48'],
        'head_tilt' => ['name' => 'Gesto: Cabeza Inclinada', 'color' => '#0D9488'],
        'restless' => ['name' => 'Movimiento Rápido / Inquietud', 'color' => '#0284C7'],
        'multiple_people' => ['name' => 'Múltiples Personas Detectadas', 'color' => '#6366F1'],
        'group_interaction' => ['name' => 'Interacción / Conversación en Grupo', 'color' => '#9333EA'],
        'attentive' => ['name' => 'Persona Atenta / Presente', 'color' => '#2563EB'],
        'absent' => ['name' => 'Persona Ausente / Sin Detección', 'color' => '#475569'],
    ];

    /**
     * Color definitions for hair tones.
     */
    public const HAIR_COLORS = [
        'hair_black' => ['name' => 'Cabello Negro / Ébano', 'color' => '#1E293B'],
        'hair_dark_brown' => ['name' => 'Cabello Castaño Oscuro', 'color' => '#78350F'],
        'hair_light_brown' => ['name' => 'Cabello Castaño Claro', 'color' => '#B45309'],
        'hair_blonde' => ['name' => 'Cabello Rubio / Dorado', 'color' => '#EAB308'],
        'hair_red' => ['name' => 'Cabello Rojizo / Cobrizo', 'color' => '#DC2626'],
        'hair_gray' => ['name' => 'Cabello Canoso / Plateado', 'color' => '#94A3B8'],
    ];

    /**
     * Render the main dashboard view.
     */
    public function index()
    {
        $recentLogs = DetectionLog::orderBy('id', 'desc')->take(15)->get();
        $stats = $this->calculateStats();

        $reverbConfig = [
            'key' => config('reverb.apps.apps.0.key', env('REVERB_APP_KEY', 'vvydtnpx84kupiwqhwrd')),
            'host' => config('reverb.servers.reverb.hostname', env('REVERB_HOST', 'localhost')),
            'port' => config('reverb.servers.reverb.port', env('REVERB_PORT', 8080)),
            'scheme' => env('REVERB_SCHEME', 'http'),
        ];

        return view('detection', [
            'recentLogs' => $recentLogs,
            'stats' => $stats,
            'objectColors' => self::OBJECT_COLORS,
            'behaviorColors' => self::BEHAVIOR_COLORS,
            'hairColors' => self::HAIR_COLORS,
            'reverbConfig' => $reverbConfig,
        ]);
    }

    /**
     * Store a new detection and broadcast via WebSocket.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category' => 'required|string|max:32',
            'label' => 'required|string|max:64',
            'display_name' => 'required|string|max:128',
            'confidence' => 'required|numeric|min:0|max:1',
            'color' => 'required|string|max:32',
            'details' => 'nullable|array',
        ]);

        $log = DetectionLog::create([
            'category' => $validated['category'],
            'label' => $validated['label'],
            'display_name' => $validated['display_name'],
            'confidence' => (float) $validated['confidence'],
            'color' => $validated['color'],
            'details' => $validated['details'] ?? null,
        ]);

        $payload = [
            'id' => $log->id,
            'category' => $log->category,
            'label' => $log->label,
            'display_name' => $log->display_name,
            'confidence' => round($log->confidence * 100, 1),
            'color' => $log->color,
            'details' => $log->details,
            'timestamp' => $log->created_at->format('H:i:s'),
            'created_at' => $log->created_at->toIso8601String(),
        ];

        $broadcasted = false;
        try {
            broadcast(new DetectionDetected($payload));
            $broadcasted = true;
        } catch (\Throwable $e) {
            Log::warning('WebSocket broadcast error: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'detection' => $payload,
            'broadcasted' => $broadcasted,
        ]);
    }

    /**
     * Fetch recent detection logs with optional pagination.
     */
    public function logs(Request $request): JsonResponse
    {
        $limit = max(1, min((int) $request->input('limit', 50), 50));
        $offset = max(0, (int) $request->input('offset', 0));
        if ($request->has('page')) {
            $page = max(1, (int) $request->input('page'));
            $offset = ($page - 1) * $limit;
        }

        $query = DetectionLog::orderBy('id', 'desc');
        $total = $query->count();
        $logs = $query->skip($offset)->take($limit)->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'category' => $item->category,
                'label' => $item->label,
                'display_name' => $item->display_name,
                'confidence' => round($item->confidence * 100, 1),
                'color' => $item->color,
                'details' => $item->details,
                'timestamp' => $item->created_at->format('H:i:s'),
                'created_at' => $item->created_at->toIso8601String(),
            ];
        });

        $currentPage = $limit > 0 ? (int) floor($offset / $limit) + 1 : 1;
        $lastPage = $limit > 0 ? max(1, (int) ceil($total / $limit)) : 1;

        return response()->json([
            'logs' => $logs,
            'total' => $total,
            'page' => $currentPage,
            'last_page' => $lastPage,
            'offset' => $offset,
            'limit' => $limit,
            'has_more' => ($offset + $limit) < $total,
            'stats' => $this->calculateStats(),
        ]);
    }

    /**
     * Clear all detection logs.
     */
    public function clear(): JsonResponse
    {
        DetectionLog::truncate();

        return response()->json([
            'success' => true,
            'message' => 'Historial de detecciones limpiado.',
        ]);
    }

    /**
     * Helper to compute statistics.
     */
    private function calculateStats(): array
    {
        $total = DetectionLog::count();
        $totalObjects = DetectionLog::where('category', 'object')->count();
        $totalBehaviors = DetectionLog::whereIn('category', ['behavior', 'gesture'])->count();
        $totalHair = DetectionLog::where('category', 'hair')->count();

        $topObjects = DetectionLog::where('category', 'object')
            ->selectRaw('display_name, color, count(*) as count')
            ->groupBy('display_name', 'color')
            ->orderByDesc('count')
            ->take(6)
            ->get();

        $topBehaviors = DetectionLog::whereIn('category', ['behavior', 'gesture', 'hair'])
            ->selectRaw('display_name, color, count(*) as count')
            ->groupBy('display_name', 'color')
            ->orderByDesc('count')
            ->take(6)
            ->get();

        return [
            'total' => $total,
            'objects' => $totalObjects,
            'behaviors' => $totalBehaviors,
            'hair' => $totalHair,
            'top_objects' => $topObjects,
            'top_behaviors' => $topBehaviors,
        ];
    }
}
