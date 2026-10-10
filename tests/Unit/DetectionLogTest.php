<?php

namespace Tests\Unit;

use App\Models\DetectionLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DetectionLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_detection_log_creation_and_attributes(): void
    {
        $log = DetectionLog::create([
            'category' => 'object',
            'label' => 'cell phone',
            'display_name' => 'Teléfono Celular',
            'confidence' => 0.985,
            'color' => '#F59E0B',
            'details' => ['track_id' => 1, 'position' => 'Centro-Medio', 'status' => 'activo'],
        ]);

        $this->assertNotNull($log->id);
        $this->assertEquals('object', $log->category);
        $this->assertEquals('cell phone', $log->label);
        $this->assertEquals('Teléfono Celular', $log->display_name);
        $this->assertEquals(0.985, $log->confidence);
        $this->assertEquals('#F59E0B', $log->color);
        $this->assertIsArray($log->details);
        $this->assertEquals(1, $log->details['track_id']);
        $this->assertEquals('Centro-Medio', $log->details['position']);
    }

    public function test_detection_log_details_json_casting(): void
    {
        $details = [
            'body_landmarks' => 33,
            'posture' => 'De Pie (Cuerpo Completo)',
            'full_body' => true,
        ];

        $log = DetectionLog::create([
            'category' => 'pose',
            'label' => 'full_body_standing',
            'display_name' => 'Persona (Cuerpo Completo: De Pie)',
            'confidence' => 0.99,
            'color' => '#06B6D4',
            'details' => $details,
        ]);

        $log->refresh();

        $this->assertIsArray($log->details);
        $this->assertEquals(33, $log->details['body_landmarks']);
        $this->assertEquals('De Pie (Cuerpo Completo)', $log->details['posture']);
        $this->assertTrue($log->details['full_body']);
    }

    public function test_detection_log_fillable_protection(): void
    {
        $log = new DetectionLog();
        $expectedFillable = ['category', 'label', 'display_name', 'confidence', 'color', 'details'];

        $this->assertEquals($expectedFillable, $log->getFillable());
    }

    public function test_detection_log_confidence_boundary_precision(): void
    {
        $log = DetectionLog::create([
            'category' => 'animal',
            'label' => 'dog',
            'display_name' => 'Perro (Cuerpo Completo)',
            'confidence' => 1.0,
            'color' => '#10B981',
            'details' => ['species' => 'canine', 'full_body' => true],
        ]);

        $this->assertEquals(1.0, (float) $log->confidence);
    }
}
