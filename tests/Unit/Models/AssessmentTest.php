<?php

use App\Models\Assessment;
use App\Models\AssessmentType;
use App\Models\CourseOffering;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssessmentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_an_assessment_with_valid_data()
    {
        $courseOffering = CourseOffering::factory()->create();
        $assessmentType = AssessmentType::factory()->create();

        $assessment = Assessment::factory()->create([
            'course_offering_id' => $courseOffering->id,
            'type_id' => $assessmentType->id,
            'title' => 'Midterm Exam',
            'description' => 'Comprehensive midterm exam',
            'total_points' => 100,
            'duration' => 90,
            'due_date' => now()->addDays(7),
            'is_published' => false,
        ]);

        $this->assertInstanceOf(Assessment::class, $assessment);
        $this->assertEquals('Midterm Exam', $assessment->title);
        $this->assertEquals(100, $assessment->total_points);
        $this->assertEquals(90, $assessment->duration);
        $this->assertEquals($courseOffering->id, $assessment->course_offering_id);
        $this->assertEquals($assessmentType->id, $assessment->type_id);
    }

    /** @test */
    public function it_has_type_relationship()
    {
        $assessmentType = AssessmentType::factory()->create();
        $assessment = Assessment::factory()->create(['assessment_type_id' => $assessmentType->id]);

        $this->assertInstanceOf(AssessmentType::class, $assessment->assessmentType);
        $this->assertEquals($assessmentType->id, $assessment->assessmentType->id);
    }

    /** @test */
    public function it_has_course_offering_relationship()
    {
        $courseOffering = CourseOffering::factory()->create();
        $assessment = Assessment::factory()->create(['course_offering_id' => $courseOffering->id]);

        $this->assertInstanceOf(CourseOffering::class, $assessment->courseOffering);
        $this->assertEquals($courseOffering->id, $assessment->courseOffering->id);
    }

    /** @test */
    public function it_can_check_if_assessment_is_published()
    {
        $publishedAssessment = Assessment::factory()->create(['is_published' => true]);
        $draftAssessment = Assessment::factory()->create(['is_published' => false]);

        $this->assertTrue($publishedAssessment->is_published);
        $this->assertFalse($draftAssessment->is_published);
    }

    /** @test */
    public function it_can_check_if_assessment_is_past_due()
    {
        $pastDueAssessment = Assessment::factory()->create([
            'due_date' => now()->subDays(1),
        ]);

        $futureAssessment = Assessment::factory()->create([
            'due_date' => now()->addDays(1),
        ]);

        $this->assertTrue($pastDueAssessment->due_date->isPast());
        $this->assertFalse($futureAssessment->due_date->isPast());
    }

    /** @test */
    public function it_can_get_total_points()
    {
        $assessment = Assessment::factory()->create([
            'total_points' => 100,
        ]);

        $this->assertEquals(100, $assessment->getTotalPoints());
    }

    /** @test */
    public function it_can_check_if_assessment_has_time_limit()
    {
        $timedAssessment = Assessment::factory()->create([
            'time_limit_minutes' => 90,
        ]);

        $unlimitedAssessment = Assessment::factory()->create([
            'time_limit_minutes' => null,
        ]);

        $this->assertTrue($timedAssessment->hasTimeLimit());
        $this->assertFalse($unlimitedAssessment->hasTimeLimit());
    }
}
