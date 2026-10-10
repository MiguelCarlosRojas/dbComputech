<?php

namespace App\Http\Controllers;

use App\Events\DetectionDetected;
use App\Models\DetectionLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DetectionController extends Controller
{
    /**
     * Color definitions for objects.
     */
    public const OBJECT_COLORS = [
        'person' => ['name' => 'Persona', 'color' => '#3B82F6'],
        'cell phone' => ['name' => 'Celular', 'color' => '#F59E0B'],
        'laptop' => ['name' => 'Laptop', 'color' => '#10B981'],
        'tv' => ['name' => 'Monitor', 'color' => '#06B6D4'],
        'bottle' => ['name' => 'Botella', 'color' => '#8B5CF6'],
        'cup' => ['name' => 'Taza', 'color' => '#14B8A6'],
        'book' => ['name' => 'Libro', 'color' => '#EC4899'],
        'backpack' => ['name' => 'Mochila', 'color' => '#6366F1'],
        'handbag' => ['name' => 'Cartera', 'color' => '#D946EF'],
        'suitcase' => ['name' => 'Maleta', 'color' => '#8B5CF6'],
        'chair' => ['name' => 'Silla', 'color' => '#64748B'],
        'couch' => ['name' => 'Sofá', 'color' => '#78716C'],
        'dining table' => ['name' => 'Mesa', 'color' => '#A855F7'],
        'potted plant' => ['name' => 'Planta', 'color' => '#22C55E'],
        'mouse' => ['name' => 'Mouse', 'color' => '#0EA5E9'],
        'keyboard' => ['name' => 'Teclado', 'color' => '#84CC16'],
        'clock' => ['name' => 'Reloj', 'color' => '#F97316'],
        'scissors' => ['name' => 'Tijeras', 'color' => '#F43F5E'],
        'remote' => ['name' => 'Control Remoto', 'color' => '#6366F1'],
        'umbrella' => ['name' => 'Paraguas', 'color' => '#0284C7'],
        'tie' => ['name' => 'Corbata', 'color' => '#4F46E5'],
        'wine glass' => ['name' => 'Copa', 'color' => '#C084FC'],
        'bowl' => ['name' => 'Tazón', 'color' => '#38BDF8'],
        'fork' => ['name' => 'Tenedor', 'color' => '#94A3B8'],
        'knife' => ['name' => 'Cuchillo', 'color' => '#F43F5E'],
        'spoon' => ['name' => 'Cuchara', 'color' => '#CBD5E1'],
        'banana' => ['name' => 'Plátano', 'color' => '#FACC15'],
        'apple' => ['name' => 'Manzana', 'color' => '#EF4444'],
        'sandwich' => ['name' => 'Sándwich', 'color' => '#D97706'],
        'orange' => ['name' => 'Naranja', 'color' => '#FB923C'],
        'broccoli' => ['name' => 'Brócoli', 'color' => '#16A34A'],
        'carrot' => ['name' => 'Zanahoria', 'color' => '#EA580C'],
        'hot dog' => ['name' => 'Hot Dog', 'color' => '#D97706'],
        'pizza' => ['name' => 'Pizza', 'color' => '#EA580C'],
        'donut' => ['name' => 'Dona', 'color' => '#EC4899'],
        'cake' => ['name' => 'Pastel', 'color' => '#F472B6'],
        'bed' => ['name' => 'Cama', 'color' => '#6366F1'],
        'toilet' => ['name' => 'Inodoro', 'color' => '#64748B'],
        'microwave' => ['name' => 'Microondas', 'color' => '#0284C7'],
        'oven' => ['name' => 'Horno', 'color' => '#B45309'],
        'toaster' => ['name' => 'Tostadora', 'color' => '#78716C'],
        'sink' => ['name' => 'Lavabo', 'color' => '#0EA5E9'],
        'refrigerator' => ['name' => 'Refrigerador', 'color' => '#0284C7'],
        'vase' => ['name' => 'Florero', 'color' => '#A855F7'],
        'hair drier' => ['name' => 'Secador', 'color' => '#E11D48'],
        'toothbrush' => ['name' => 'Cepillo Dental', 'color' => '#10B981'],
        'teddy bear' => ['name' => 'Peluche', 'color' => '#F59E0B'],
        'cat' => ['name' => 'Gato', 'color' => '#FB923C'],
        'dog' => ['name' => 'Perro', 'color' => '#D97706'],
        'sports ball' => ['name' => 'Pelota', 'color' => '#EAB308'],
        'car' => ['name' => 'Automóvil', 'color' => '#3B82F6'],
        'bicycle' => ['name' => 'Bicicleta', 'color' => '#10B981'],
        'motorcycle' => ['name' => 'Motocicleta', 'color' => '#EF4444'],
        'airplane' => ['name' => 'Avión', 'color' => '#0284C7'],
        'bus' => ['name' => 'Autobús', 'color' => '#F59E0B'],
        'train' => ['name' => 'Tren', 'color' => '#6366F1'],
        'truck' => ['name' => 'Camión', 'color' => '#0D9488'],
        'boat' => ['name' => 'Barco', 'color' => '#0284C7'],
        'traffic light' => ['name' => 'Semáforo', 'color' => '#EAB308'],
        'fire hydrant' => ['name' => 'Hidrante', 'color' => '#EF4444'],
        'stop sign' => ['name' => 'Señal Pare', 'color' => '#DC2626'],
        'parking meter' => ['name' => 'Parquímetro', 'color' => '#64748B'],
        'bench' => ['name' => 'Banca', 'color' => '#475569'],
        'bird' => ['name' => 'Pájaro', 'color' => '#06B6D4'],
        'horse' => ['name' => 'Caballo', 'color' => '#B45309'],
        'sheep' => ['name' => 'Oveja', 'color' => '#E2E8F0'],
        'cow' => ['name' => 'Vaca', 'color' => '#475569'],
        'elephant' => ['name' => 'Elefante', 'color' => '#64748B'],
        'bear' => ['name' => 'Oso', 'color' => '#78350F'],
        'zebra' => ['name' => 'Cebra', 'color' => '#334155'],
        'giraffe' => ['name' => 'Jirafa', 'color' => '#CA8A04'],
        'frisbee' => ['name' => 'Frisbee', 'color' => '#F43F5E'],
        'skis' => ['name' => 'Esquís', 'color' => '#0284C7'],
        'snowboard' => ['name' => 'Snowboard', 'color' => '#6366F1'],
        'kite' => ['name' => 'Cometa', 'color' => '#EC4899'],
        'baseball bat' => ['name' => 'Bate', 'color' => '#B45309'],
        'baseball glove' => ['name' => 'Guante', 'color' => '#78350F'],
        'skateboard' => ['name' => 'Patineta', 'color' => '#10B981'],
        'surfboard' => ['name' => 'Tabla de Surf', 'color' => '#06B6D4'],
        'tennis racket' => ['name' => 'Raqueta', 'color' => '#84CC16'],
        'pen' => ['name' => 'Bolígrafo', 'color' => '#2563EB'],
        'glasses' => ['name' => 'Lentes', 'color' => '#0EA5E9'],
        'watch' => ['name' => 'Reloj Pulsera', 'color' => '#F59E0B'],
        'wallet' => ['name' => 'Billetera', 'color' => '#78350F'],
        'headphones' => ['name' => 'Auriculares', 'color' => '#8B5CF6'],
        'document' => ['name' => 'Documento', 'color' => '#CBD5E1'],
    ];

    /**
     * Color definitions for behaviors & gestures (Acciones Rutinarias, Positivas y de Riesgo/Seguridad).
     */
    public const BEHAVIOR_COLORS = [
        'talking_phone' => ['name' => 'Hablando por Celular', 'color' => '#FF5722'],
        'phone_call' => ['name' => 'Llamada Activa', 'color' => '#FF3D00'],
        'holding_phone' => ['name' => 'Usando Celular', 'color' => '#F59E0B'],
        'hand_fingers' => ['name' => 'Gesto de Mano', 'color' => '#10B981'],
        'both_hands' => ['name' => 'Ambas Manos en Escena', 'color' => '#06B6D4'],
        'eating_food' => ['name' => 'Comiendo Alimento', 'color' => '#F97316'],
        'showing_fruit_veg' => ['name' => 'Mostrando Fruta', 'color' => '#84CC16'],
        'showing_food' => ['name' => 'Mostrando Comida', 'color' => '#EAB308'],
        'showing_object' => ['name' => 'Mostrando Objeto', 'color' => '#38BDF8'],
        'retrieving_item' => ['name' => 'Manipulando Mochila', 'color' => '#A855F7'],
        'using_utensil' => ['name' => 'Usando Cubierto', 'color' => '#EC4899'],
        'working_laptop' => ['name' => 'Trabajando en Laptop', 'color' => '#059669'],
        'typing_keyboard' => ['name' => 'Escribiendo en Teclado', 'color' => '#10B981'],
        'drinking' => ['name' => 'Bebiendo', 'color' => '#7C3AED'],
        'reading' => ['name' => 'Leyendo Documento', 'color' => '#DB2777'],
        'writing' => ['name' => 'Escribiendo Notas', 'color' => '#EC4899'],
        'hands_up' => ['name' => 'Manos Arriba', 'color' => '#DC2626'],
        'waving' => ['name' => 'Saludando con Mano', 'color' => '#F59E0B'],
        'thumbs_up' => ['name' => 'Pulgar Arriba', 'color' => '#22C55E'],
        'thinking' => ['name' => 'Pensando', 'color' => '#8B5CF6'],
        'face_touch' => ['name' => 'Mano en Rostro', 'color' => '#E11D48'],
        'head_tilt' => ['name' => 'Cabeza Inclinada', 'color' => '#0D9488'],
        'sitting_posture' => ['name' => 'Sentado', 'color' => '#3B82F6'],
        'standing_posture' => ['name' => 'De Pie', 'color' => '#06B6D4'],
        'restless' => ['name' => 'Movimiento Rápido', 'color' => '#0284C7'],
        'multiple_people' => ['name' => 'Múltiples Personas', 'color' => '#6366F1'],
        'group_interaction' => ['name' => 'Conversación en Grupo', 'color' => '#9333EA'],
        'attentive' => ['name' => 'Persona Presente', 'color' => '#2563EB'],
        'presentation' => ['name' => 'Exponiendo Presentación', 'color' => '#38BDF8'],
        'stretching' => ['name' => 'Descanso Activo', 'color' => '#14B8A6'],
        'taking_notes' => ['name' => 'Tomando Apuntes', 'color' => '#EC4899'],
        'handling_sharp' => ['name' => 'Manipulando Objeto Peligroso', 'color' => '#E11D48'],
        'face_hidden' => ['name' => 'Ocultando Rostro', 'color' => '#BE123C'],
        'head_in_hands' => ['name' => 'Manos en Cabeza', 'color' => '#C026D3'],
        'aggressive_motion' => ['name' => 'Movimiento Brusco', 'color' => '#EF4444'],
        'rummaging_bags' => ['name' => 'Manipulando Pertenencias', 'color' => '#9333EA'],
        'unattended_object' => ['name' => 'Objeto Desatendido', 'color' => '#F59E0B'],
        'person_fallen' => ['name' => 'Persona Caída', 'color' => '#DC2626'],
        'sleeping_desk' => ['name' => 'Inactividad en Escritorio', 'color' => '#64748B'],
        'absent' => ['name' => 'Persona Ausente', 'color' => '#475569'],
    ];

    /**
     * Color definitions for hair tones.
     */
    public const HAIR_COLORS = [
        'hair_black' => ['name' => 'Cabello Negro', 'color' => '#1E293B'],
        'hair_dark_brown' => ['name' => 'Cabello Castaño Oscuro', 'color' => '#78350F'],
        'hair_light_brown' => ['name' => 'Cabello Castaño Claro', 'color' => '#B45309'],
        'hair_blonde' => ['name' => 'Cabello Rubio', 'color' => '#EAB308'],
        'hair_red' => ['name' => 'Cabello Rojizo', 'color' => '#DC2626'],
        'hair_gray' => ['name' => 'Cabello Canoso', 'color' => '#94A3B8'],
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

    /**
     * Proxy for ESP32-CAM and IP camera JPEG snapshots.
     * Bypasses browser CORS restrictions to allow AI canvas processing.
     */
    public function snapshotProxy(Request $request)
    {
        $url = $request->query('url');
        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            return response()->json(['error' => 'URL de cámara no válida.'], 400);
        }

        try {
            $response = Http::timeout(4)->withoutVerifying()->get($url);
            if ($response->successful()) {
                $contentType = $response->header('Content-Type') ?: 'image/jpeg';
                return response($response->body(), 200, [
                    'Content-Type' => $contentType,
                    'Access-Control-Allow-Origin' => '*',
                    'Access-Control-Allow-Methods' => 'GET, OPTIONS',
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0',
                ]);
            }
            return response()->json(['error' => 'No se pudo obtener el fotograma de la cámara.'], 502);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Error al conectar con la cámara: ' . $e->getMessage()], 502);
        }
    }

    /**
     * Proxy for ESP32-CAM and IP camera MJPEG video stream.
     * Streams multipart/x-mixed-replace data directly with CORS headers.
     */
    public function streamProxy(Request $request)
    {
        $url = $request->query('url');
        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            return response()->json(['error' => 'URL de stream no válida.'], 400);
        }

        $context = stream_context_create([
            'http' => [
                'timeout' => 8,
                'follow_location' => 1,
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ]
        ]);

        $stream = @fopen($url, 'r', false, $context);
        if (!$stream) {
            return response()->json(['error' => 'No se pudo abrir el stream de la cámara ESP32-CAM.'], 502);
        }

        return new StreamedResponse(function () use ($stream) {
            try {
                while (!feof($stream) && connection_status() === CONNECTION_NORMAL) {
                    $chunk = fread($stream, 8192);
                    if ($chunk !== false && strlen($chunk) > 0) {
                        echo $chunk;
                        flush();
                    }
                }
            } finally {
                if (is_resource($stream)) {
                    fclose($stream);
                }
            }
        }, 200, [
            'Content-Type' => 'multipart/x-mixed-replace; boundary=frame',
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, OPTIONS',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}
