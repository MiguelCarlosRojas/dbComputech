<?php

namespace Tests\Feature;

use App\Events\DetectionDetected;
use App\Models\DetectionLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class DetectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_detection_dashboard_loads_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('dbCOMPUTECH');
        $response->assertSee('Detección de Objetos');
        $response->assertSee('Transmisión de Video');
    }

    public function test_can_store_object_detection_and_broadcast_event(): void
    {
        Event::fake([DetectionDetected::class]);

        $payload = [
            'category' => 'object',
            'label' => 'cell phone',
            'display_name' => 'Teléfono Celular',
            'confidence' => 0.94,
            'color' => '#F59E0B',
            'details' => ['bbox' => [100, 150, 60, 120]],
        ];

        $response = $this->postJson('/api/detections', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'detection' => [
                'category' => 'object',
                'label' => 'cell phone',
                'color' => '#F59E0B',
            ],
        ]);

        $this->assertDatabaseHas('detection_logs', [
            'label' => 'cell phone',
            'color' => '#F59E0B',
            'category' => 'object',
        ]);

        Event::assertDispatched(DetectionDetected::class, function ($event) {
            return $event->detection['label'] === 'cell phone'
                && $event->detection['color'] === '#F59E0B';
        });
    }

    public function test_can_store_behavior_detection(): void
    {
        Event::fake([DetectionDetected::class]);

        $payload = [
            'category' => 'behavior',
            'label' => 'talking_phone',
            'display_name' => 'Hablando por Teléfono',
            'confidence' => 0.95,
            'color' => '#FF5722',
            'details' => ['status' => 'active'],
        ];

        $response = $this->postJson('/api/detections', $payload);

        $response->assertStatus(200);
        $this->assertDatabaseHas('detection_logs', [
            'category' => 'behavior',
            'label' => 'talking_phone',
            'color' => '#FF5722',
        ]);

        Event::assertDispatched(DetectionDetected::class);
    }

    public function test_can_store_hair_tone_and_gesture_detection(): void
    {
        Event::fake([DetectionDetected::class]);

        $hairPayload = [
            'category' => 'hair',
            'label' => 'hair_dark_brown',
            'display_name' => 'Cabello Castaño Oscuro',
            'confidence' => 0.92,
            'color' => '#78350F',
            'details' => ['rgb' => 'rgb(75,45,28)'],
        ];

        $response = $this->postJson('/api/detections', $hairPayload);
        $response->assertStatus(200);
        $this->assertDatabaseHas('detection_logs', [
            'category' => 'hair',
            'label' => 'hair_dark_brown',
            'color' => '#78350F',
        ]);

        $gesturePayload = [
            'category' => 'gesture',
            'label' => 'hands_up',
            'display_name' => 'Gesto: Manos Arriba / Alerta',
            'confidence' => 0.93,
            'color' => '#DC2626',
        ];

        $resGesture = $this->postJson('/api/detections', $gesturePayload);
        $resGesture->assertStatus(200);
        $this->assertDatabaseHas('detection_logs', [
            'category' => 'gesture',
            'label' => 'hands_up',
            'color' => '#DC2626',
        ]);
    }

    public function test_can_fetch_and_clear_detection_logs(): void
    {
        DetectionLog::create([
            'category' => 'object',
            'label' => 'laptop',
            'display_name' => 'Laptop / Computadora',
            'confidence' => 0.98,
            'color' => '#10B981',
        ]);

        $getResponse = $this->getJson('/api/detections');
        $getResponse->assertStatus(200);
        $getResponse->assertJsonCount(1, 'logs');

        $deleteResponse = $this->deleteJson('/api/detections');
        $deleteResponse->assertStatus(200);

        $this->assertDatabaseCount('detection_logs', 0);
    }
}
