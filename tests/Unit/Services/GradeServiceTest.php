<?php

namespace Tests\Unit\Services;

use App\Models\Assessment;
use App\Models\AssessmentType;
use App\Models\CourseOffering;
use App\Models\StudentGrade;
use App\Models\User;
use App\Services\GradeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradeServiceTest extends TestCase
{
    use RefreshDatabase;

    private GradeService $service;

    private User $student;

    private Assessment $assessment;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles if they don't exist
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);

        $this->service = app(GradeService::class);
        $this->student = User::factory()->student()->create();
        $assessmentType = AssessmentType::factory()->create(['weight' => 1]);
        $courseOffering = CourseOffering::factory()->create();
        $this->assessment = Assessment::factory()->create([
            'course_offering_id' => $courseOffering->id,
            'assessment_type_id' => $assessmentType->id,
            'max_score' => 100,
            'is_published' => true,
        ]);
    }

    /** @test */
    public function it_can_record_a_grade()
    {
        $grade = $this->service->recordGrade(
            $this->student->id,
            $this->assessment->id,
            85.5,
            1,
            'Good work'
        );

        $this->assertInstanceOf(StudentGrade::class, $grade);
        $this->assertEquals($this->student->id, $grade->student_id);
        $this->assertEquals($this->assessment->id, $grade->assessment_id);
        $this->assertEquals(85.5, $grade->grade);
        $this->assertEquals('Good work', $grade->feedback);
    }

    /** @test */
    public function it_updates_existing_grade()
    {
        $this->service->recordGrade($this->student->id, $this->assessment->id, 80);

        $updatedGrade = $this->service->recordGrade($this->student->id, $this->assessment->id, 90);

        $this->assertEquals(90, $updatedGrade->grade);
        $this->assertEquals($this->assessment->id, $updatedGrade->assessment_id);
    }

    /** @test */
    public function it_validates_score_range()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Score must be between 0 and 100');

        $this->service->recordGrade($this->student->id, $this->assessment->id, 150);
    }

    /** @test */
    public function it_sanitizes_feedback_input()
    {
        $grade = $this->service->recordGrade(
            $this->student->id,
            $this->assessment->id,
            85,
            1,
            '<script>alert("xss")</script>Good work'
        );

        $this->assertEquals('alert("xss")Good work', $grade->feedback);
    }

    /** @test */
    public function it_calculates_letter_grade_correctly()
    {
        config(['grading.scale' => [
            'A' => 93, 'A-' => 90, 'B+' => 87, 'B' => 83, 'B-' => 80,
            'C+' => 77, 'C' => 73, 'C-' => 70, 'D+' => 67, 'D' => 63, 'D-' => 60,
        ]]);

        $this->assertEquals('A', $this->invokeMethod($this->service, 'percentageToLetterGrade', [95]));
        $this->assertEquals('B+', $this->invokeMethod($this->service, 'percentageToLetterGrade', [88]));
        $this->assertEquals('C', $this->invokeMethod($this->service, 'percentageToLetterGrade', [75]));
        $this->assertEquals('F', $this->invokeMethod($this->service, 'percentageToLetterGrade', [50]));
    }

    /** @test */
    public function it_calculates_grade_points_correctly()
    {
        config(['grading.grade_points' => [
            'A' => 4.0, 'A-' => 3.7, 'B+' => 3.3, 'B' => 3.0, 'B-' => 2.7,
            'C+' => 2.3, 'C' => 2.0, 'C-' => 1.7, 'D+' => 1.3, 'D' => 1.0, 'D-' => 0.7, 'F' => 0.0,
        ]]);

        $this->assertEquals(4.0, $this->invokeMethod($this->service, 'percentageToGradePoints', [95]));
        $this->assertEquals(3.0, $this->invokeMethod($this->service, 'percentageToGradePoints', [85]));
        $this->assertEquals(0.0, $this->invokeMethod($this->service, 'percentageToGradePoints', [50]));
    }

    /** @test */
    public function it_calculates_median_correctly()
    {
        $this->assertEquals(3.0, $this->invokeMethod($this->service, 'calculateMedian', [[1, 2, 3, 4, 5]]));
        $this->assertEquals(3.5, $this->invokeMethod($this->service, 'calculateMedian', [[1, 2, 3, 4, 5, 6]]));
    }

    /** @test */
    public function it_calculates_standard_deviation_correctly()
    {
        $scores = [2, 4, 4, 4, 5, 5, 7, 9];
        $mean = array_sum($scores) / count($scores);
        $stdDev = $this->invokeMethod($this->service, 'calculateStdDev', [$scores, $mean]);

        $this->assertEqualsWithDelta(2.0, $stdDev, 0.1);
    }

    /** @test */
    public function it_returns_empty_array_for_no_grades()
    {
        $stats = $this->service->getAssessmentStatistics($this->assessment->id);

        $this->assertEquals(0, $stats['count']);
        $this->assertEquals(0, $stats['average']);
    }

    /**
     * Helper method to invoke private methods
     */
    private function invokeMethod($object, string $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
