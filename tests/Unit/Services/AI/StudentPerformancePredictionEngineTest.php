<?php

namespace Tests\Unit\Services\AI;

use App\Services\AI\Contracts\PredictionResult;
use App\Services\AI\Engines\StudentPerformancePredictionEngine;
use App\Services\AI\Exceptions\DataQualityException;
use App\Services\AI\FeatureExtractors\StudentFeatureExtractor;
use App\Services\AI\Validators\PredictionValidator;
use Mockery;
use PHPUnit\Framework\TestCase;

class StudentPerformancePredictionEngineTest extends TestCase
{
    private StudentPerformancePredictionEngine $engine;

    private $featureExtractor;

    private $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->featureExtractor = Mockery::mock(StudentFeatureExtractor::class);
        $this->validator = Mockery::mock(PredictionValidator::class);

        $this->engine = new StudentPerformancePredictionEngine(
            $this->featureExtractor,
            $this->validator
        );
    }

    /** @test */
    public function it_makes_prediction_with_valid_features()
    {
        $features = [
            'student_id' => 123,
            'course_id' => 456,
            'historical_grades' => [85, 90, 88],
            'attendance_rate' => 0.92,
            'assignments_submitted' => 8,
            'quiz_average' => 87.5,
        ];

        $processedFeatures = [
            'historical_gpa' => 3.5,
            'attendance_rate' => 0.92,
            'assignment_completion' => 0.8,
            'quiz_average' => 87.5,
            'grade_trend' => 2.5,
            'grade_consistency' => 5.0,
            'late_submission_rate' => 0.1,
            'course_difficulty' => 0.3,
        ];

        $this->validator->shouldReceive('validate')
            ->once()
            ->andReturn(new \App\Services\AI\Contracts\ValidationResult(true));

        $this->featureExtractor->shouldReceive('extract')
            ->once()
            ->with($features)
            ->andReturn($processedFeatures);

        $result = $this->engine->predict($features);

        $this->assertInstanceOf(PredictionResult::class, $result);
        $this->assertGreaterThanOrEqual(0, $result->value);
        $this->assertLessThanOrEqual(100, $result->value);
        $this->assertGreaterThanOrEqual(0.1, $result->confidence);
        $this->assertLessThanOrEqual(0.99, $result->confidence);
        $this->assertContains($result->method, ['ml_ensemble', 'rule_based']);
    }

    /** @test */
    public function it_throws_exception_for_invalid_features()
    {
        $features = [
            'student_id' => 'invalid',
            'course_id' => 456,
        ];

        $this->validator->shouldReceive('validate')
            ->once()
            ->andReturn(new \App\Services\AI\Contracts\ValidationResult(
                false,
                ['student_id must be an integer']
            ));

        $this->expectException(DataQualityException::class);

        $this->engine->predict($features);
    }

    /** @test */
    public function it_uses_fallback_when_ml_service_fails()
    {
        $features = [
            'student_id' => 123,
            'course_id' => 456,
            'historical_average' => 75,
            'recent_trend' => 5,
            'attendance_rate' => 0.85,
        ];

        $processedFeatures = [
            'historical_gpa' => 3.0,
            'attendance_rate' => 0.85,
            'assignment_completion' => 0.75,
            'quiz_average' => 78,
            'grade_trend' => 5,
            'grade_consistency' => 6.0,
            'late_submission_rate' => 0.15,
            'course_difficulty' => 0.4,
        ];

        $this->validator->shouldReceive('validate')
            ->once()
            ->andReturn(new \App\Services\AI\Contracts\ValidationResult(true));

        $this->featureExtractor->shouldReceive('extract')
            ->once()
            ->with($features)
            ->andReturn($processedFeatures);

        $result = $this->engine->predict($features);

        $this->assertInstanceOf(PredictionResult::class, $result);
        $this->assertEquals('rule_based', $result->method);
    }

    /** @test */
    public function it_calibrates_confidence_based_on_feature_quality()
    {
        $features = [
            'student_id' => 123,
            'course_id' => 456,
            'historical_grades' => [85, 90, 88, 92, 87],
            'attendance_rate' => 0.95,
            'assignments_submitted' => 10,
            'quiz_average' => 90,
        ];

        $processedFeatures = [
            'historical_gpa' => 3.7,
            'attendance_rate' => 0.95,
            'assignment_completion' => 1.0,
            'quiz_average' => 90,
            'grade_trend' => 3.0,
            'grade_consistency' => 3.0,
            'late_submission_rate' => 0.0,
            'course_difficulty' => 0.2,
            'semester_count' => 5,
            'days_since_enrollment' => 60,
        ];

        $this->validator->shouldReceive('validate')
            ->once()
            ->andReturn(new \App\Services\AI\Contracts\ValidationResult(true));

        $this->featureExtractor->shouldReceive('extract')
            ->once()
            ->with($features)
            ->andReturn($processedFeatures);

        $result = $this->engine->predict($features);

        $this->assertInstanceOf(PredictionResult::class, $result);
        // High quality features should result in higher confidence
        $this->assertGreaterThan(0.6, $result->confidence);
    }

    /** @test */
    public function it_handles_batch_predictions()
    {
        $batchFeatures = [
            ['student_id' => 123, 'course_id' => 456],
            ['student_id' => 124, 'course_id' => 456],
            ['student_id' => 125, 'course_id' => 456],
        ];

        $this->validator->shouldReceive('validate')
            ->times(3)
            ->andReturn(new \App\Services\AI\Contracts\ValidationResult(true));

        $this->featureExtractor->shouldReceive('extract')
            ->times(3)
            ->andReturn([
                'historical_gpa' => 3.0,
                'attendance_rate' => 0.8,
                'assignment_completion' => 0.7,
                'quiz_average' => 75,
            ]);

        $results = $this->engine->batchPredict($batchFeatures);

        $this->assertCount(3, $results);
        foreach ($results as $result) {
            $this->assertInstanceOf(PredictionResult::class, $result);
        }
    }

    /** @test */
    public function it_returns_model_info()
    {
        $modelInfo = $this->engine->getModelInfo();

        $this->assertEquals('Student Performance Predictor', $modelInfo->name);
        $this->assertEquals('ensemble', $modelInfo->type);
        $this->assertContains('classification', $modelInfo->capabilities);
        $this->assertContains('regression', $modelInfo->capabilities);
    }

    /** @test */
    public function it_validates_features_correctly()
    {
        $validFeatures = [
            'student_id' => 123,
            'course_id' => 456,
            'historical_grades' => [85, 90],
            'attendance_rate' => 0.9,
            'assignments_submitted' => 8,
            'quiz_average' => 85,
        ];

        $this->validator->shouldReceive('validate')
            ->once()
            ->andReturn(new \App\Services\AI\Contracts\ValidationResult(true));

        $validation = $this->engine->validateFeatures($validFeatures);

        $this->assertTrue($validation->passed());
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
