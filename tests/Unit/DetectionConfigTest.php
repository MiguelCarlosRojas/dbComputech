<?php

namespace Tests\Unit;

use App\Events\DetectionDetected;
use App\Http\Controllers\DetectionController;
use Tests\TestCase;

class DetectionConfigTest extends TestCase
{
    public function test_object_colors_constants_are_valid(): void
    {
        $colors = DetectionController::OBJECT_COLORS;

        $this->assertIsArray($colors);
        $this->assertGreaterThanOrEqual(20, count($colors));

        foreach ($colors as $key => $item) {
            $this->assertArrayHasKey('name', $item, "Object $key lacks name");
            $this->assertArrayHasKey('color', $item, "Object $key lacks color");
            $this->assertNotEmpty($item['name'], "Object $key has empty name");
            $this->assertMatchesRegularExpression('/^#[0-9A-Fa-f]{6}$/', $item['color'], "Object $key has invalid color format");
        }
    }

    public function test_behavior_colors_constants_are_valid(): void
    {
        $behaviors = DetectionController::BEHAVIOR_COLORS;

        $this->assertIsArray($behaviors);
        $this->assertGreaterThanOrEqual(30, count($behaviors));

        foreach ($behaviors as $key => $item) {
            $this->assertArrayHasKey('name', $item, "Behavior $key lacks name");
            $this->assertArrayHasKey('color', $item, "Behavior $key lacks color");
            $this->assertNotEmpty($item['name'], "Behavior $key has empty name");
            $this->assertMatchesRegularExpression('/^#[0-9A-Fa-f]{6}$/', $item['color'], "Behavior $key has invalid color format");
        }
    }

    public function test_hair_colors_constants_are_valid(): void
    {
        $hairTones = DetectionController::HAIR_COLORS;

        $this->assertIsArray($hairTones);
        $this->assertCount(6, $hairTones);

        foreach ($hairTones as $key => $item) {
            $this->assertArrayHasKey('name', $item, "Hair $key lacks name");
            $this->assertArrayHasKey('color', $item, "Hair $key lacks color");
            $this->assertMatchesRegularExpression('/^#[0-9A-Fa-f]{6}$/', $item['color']);
        }
    }

    public function test_detection_detected_broadcast_payload_structure(): void
    {
        $payload = [
            'id' => 99,
            'category' => 'pose',
            'label' => 'full_body_standing',
            'display_name' => 'Persona (Cuerpo Completo: De Pie)',
            'confidence' => 99.4,
            'color' => '#06B6D4',
            'details' => ['full_body' => true, 'landmarks' => 33],
            'timestamp' => '15:30:45',
            'created_at' => '2026-10-09T15:30:45-05:00',
        ];

        $event = new DetectionDetected($payload);

        $this->assertEquals($payload, $event->detection);
        $this->assertArrayHasKey('id', $event->detection);
        $this->assertArrayHasKey('category', $event->detection);
        $this->assertArrayHasKey('display_name', $event->detection);
        $this->assertArrayHasKey('confidence', $event->detection);
        $this->assertArrayHasKey('color', $event->detection);

        $channels = $event->broadcastOn();
        $this->assertCount(1, $channels);
        $this->assertEquals('detections', $channels[0]->name);
    }
}
