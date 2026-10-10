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
        $response->assertSee('Visión IA Pro');
        $response->assertSee('Monitoreo en Vivo');
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

    public function test_can_store_full_body_pose_detection(): void
    {
        Event::fake([DetectionDetected::class]);

        $payload = [
            'category' => 'pose',
            'label' => 'full_body_standing',
            'display_name' => 'Persona: De Pie (Cuerpo Completo)',
            'confidence' => 0.99,
            'color' => '#06B6D4',
            'details' => [
                'full_body' => true,
                'landmarks' => 33,
                'posture' => 'standing',
            ],
        ];

        $response = $this->postJson('/api/detections', $payload);

        $response->assertStatus(200);
        $this->assertDatabaseHas('detection_logs', [
            'category' => 'pose',
            'label' => 'full_body_standing',
            'color' => '#06B6D4',
        ]);

        Event::assertDispatched(DetectionDetected::class);
    }

    public function test_can_store_animal_detection(): void
    {
        Event::fake([DetectionDetected::class]);

        $payload = [
            'category' => 'animal',
            'label' => 'dog',
            'display_name' => 'Perro (Cuerpo Completo • Activo)',
            'confidence' => 0.97,
            'color' => '#10B981',
            'details' => [
                'species' => 'canine',
                'full_body' => true,
            ],
        ];

        $response = $this->postJson('/api/detections', $payload);

        $response->assertStatus(200);
        $this->assertDatabaseHas('detection_logs', [
            'category' => 'animal',
            'label' => 'dog',
            'color' => '#10B981',
        ]);

        Event::assertDispatched(DetectionDetected::class);
    }

    public function test_can_store_behavior_and_finger_gesture_detection(): void
    {
        Event::fake([DetectionDetected::class]);

        $behaviorPayload = [
            'category' => 'behavior',
            'label' => 'phone_call',
            'display_name' => 'Llamada Activa en Oreja',
            'confidence' => 0.96,
            'color' => '#FF3D00',
            'details' => ['status' => 'active'],
        ];

        $resBeh = $this->postJson('/api/detections', $behaviorPayload);
        $resBeh->assertStatus(200);

        $gesturePayload = [
            'category' => 'gesture',
            'label' => 'hand_fingers',
            'display_name' => 'Mano Derecha: 3 Dedos Levantados',
            'confidence' => 0.98,
            'color' => '#10B981',
            'details' => ['fingers' => 3, 'hand' => 'Derecha'],
        ];

        $resGes = $this->postJson('/api/detections', $gesturePayload);
        $resGes->assertStatus(200);

        $this->assertDatabaseHas('detection_logs', [
            'category' => 'gesture',
            'label' => 'hand_fingers',
        ]);
    }

    public function test_can_store_hair_tone_detection(): void
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
    }

    public function test_store_validation_rejects_invalid_payloads(): void
    {
        // Falta category y label
        $res1 = $this->postJson('/api/detections', [
            'confidence' => 0.85,
            'color' => '#FFFFFF',
        ]);
        $res1->assertStatus(422);

        // Confianza negativa
        $res2 = $this->postJson('/api/detections', [
            'category' => 'object',
            'label' => 'cup',
            'display_name' => 'Taza',
            'confidence' => -0.5,
            'color' => '#14B8A6',
        ]);
        $res2->assertStatus(422);

        // Confianza mayor a 1
        $res3 = $this->postJson('/api/detections', [
            'category' => 'object',
            'label' => 'cup',
            'display_name' => 'Taza',
            'confidence' => 1.5,
            'color' => '#14B8A6',
        ]);
        $res3->assertStatus(422);
    }

    public function test_can_fetch_and_paginate_detection_logs(): void
    {
        for ($i = 1; $i <= 15; $i++) {
            DetectionLog::create([
                'category' => 'object',
                'label' => "item_{$i}",
                'display_name' => "Elemento #{$i}",
                'confidence' => 0.95,
                'color' => '#10B981',
            ]);
        }

        $resPage1 = $this->getJson('/api/detections?limit=10&offset=0');
        $resPage1->assertStatus(200);
        $resPage1->assertJsonCount(10, 'logs');
        $this->assertTrue($resPage1->json('has_more'));

        $resPage2 = $this->getJson('/api/detections?limit=10&offset=10');
        $resPage2->assertStatus(200);
        $resPage2->assertJsonCount(5, 'logs');
        $this->assertFalse($resPage2->json('has_more'));
    }

    public function test_can_clear_all_detection_logs(): void
    {
        DetectionLog::create([
            'category' => 'object',
            'label' => 'laptop',
            'display_name' => 'Laptop',
            'confidence' => 0.98,
            'color' => '#10B981',
        ]);

        $this->assertDatabaseCount('detection_logs', 1);

        $deleteResponse = $this->deleteJson('/api/detections');
        $deleteResponse->assertStatus(200);
        $deleteResponse->assertJson(['success' => true]);

        $this->assertDatabaseCount('detection_logs', 0);
    }
}
