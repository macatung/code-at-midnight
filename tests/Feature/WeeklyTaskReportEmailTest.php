<?php

namespace Tests\Feature;

use App\Mail\WeeklyTaskReportMail;
use App\Models\Project;
use App\Models\SiteSetting;
use App\Models\Sprint;
use App\Models\Task;
use App\Services\WeeklyTaskReportService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class WeeklyTaskReportEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_and_save_report_settings_via_api()
    {
        // 1. Get default settings
        $response = $this->getJson('/api/tasks/report-settings');
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'is_enabled' => false,
                         'day_of_week' => 'monday',
                         'send_time' => '08:00',
                     ],
                 ]);

        // 2. Save new settings
        $saveResponse = $this->postJson('/api/tasks/report-settings', [
            'is_enabled' => true,
            'recipients' => 'boss@company.com, manager@company.com',
            'day_of_week' => 'monday',
            'send_time' => '08:00',
            'report_title' => 'Báo Cáo Tiến Độ Dự Án Đầu Tuần',
            'include_upcoming' => true,
            'include_warnings' => true,
        ]);

        $saveResponse->assertStatus(200)
                     ->assertJson([
                         'success' => true,
                         'data' => [
                             'is_enabled' => true,
                             'recipients' => 'boss@company.com, manager@company.com',
                         ],
                     ]);

        $savedRaw = SiteSetting::get('task_report_settings');
        $this->assertNotNull($savedRaw);
        $this->assertStringContainsString('boss@company.com', $savedRaw);
    }

    public function test_weekly_report_service_aggregates_correct_kpis_without_pomodoro()
    {
        $project = Project::create([
            'title' => 'Hệ Thống Core Banking',
            'tagline' => 'Hệ thống ngân hàng lõi thế hệ mới',
            'description' => 'Dự án trọng điểm chuyển đổi số ngân hàng',
            'slug' => 'he-thong-core-banking',
            'key' => 'BANK',
            'type' => 'work',
            'status' => 'active',
            'color' => '#2563eb',
        ]);

        $sprint = Sprint::create([
            'name' => 'Sprint 1 - Foundation',
            'goal' => 'Hoàn thiện auth và database schema',
            'status' => 'active',
            'start_date' => Carbon::now()->subDays(3)->toDateString(),
            'end_date' => Carbon::now()->addDays(11)->toDateString(),
        ]);

        // Task 1: Completed within last 7 days
        Task::create([
            'project_id' => $project->id,
            'sprint_id' => $sprint->id,
            'issue_key' => 'BANK-1',
            'title' => 'Thiết kế Database Schema',
            'status' => 'done',
            'priority' => 'high',
            'story_points' => 5,
            'estimated_pomodoros' => 4,
            'completed_pomodoros' => 4,
            'completed_at' => Carbon::now()->subDays(2),
        ]);

        // Task 2: Upcoming in progress
        Task::create([
            'project_id' => $project->id,
            'sprint_id' => $sprint->id,
            'issue_key' => 'BANK-2',
            'title' => 'Tích hợp Payment Gateway',
            'status' => 'in_progress',
            'priority' => 'urgent',
            'story_points' => 8,
            'due_date' => Carbon::now()->addDays(4)->toDateString(),
        ]);

        // Task 3: Overdue task
        Task::create([
            'project_id' => $project->id,
            'sprint_id' => $sprint->id,
            'issue_key' => 'BANK-3',
            'title' => 'Viết tài liệu API',
            'status' => 'todo',
            'priority' => 'medium',
            'story_points' => 3,
            'due_date' => Carbon::now()->subDays(1)->toDateString(),
        ]);

        $service = app(WeeklyTaskReportService::class);
        $data = $service->generateReportData();

        $this->assertEquals(1, $data['kpis']['completed_tasks_count']);
        $this->assertEquals(5, $data['kpis']['completed_story_points']);
        $this->assertEquals(1, $data['kpis']['warning_tasks_count']);
        $this->assertEquals(33, $data['kpis']['sprint_progress_percent']); // 1 of 3 tasks done

        // Verify sprint metrics
        $this->assertNotNull($data['sprint_metrics']);
        $this->assertEquals('Sprint 1 - Foundation', $data['sprint_metrics']['name']);

        // Verify Blade template renders cleanly without pomodoro mentions
        $mailable = new WeeklyTaskReportMail($data, 'Test Weekly Report');
        $html = $mailable->render();

        $this->assertStringContainsString('Thiết kế Database Schema', $html);
        $this->assertStringContainsString('BANK-1', $html);
        $this->assertStringContainsString('5 pts', $html);
        $this->assertStringContainsString('BANK-3', $html);
        $this->assertStringContainsString('Quá hạn 1 ngày', $html);

        // Crucial requirement: No pomodoro in executive boss report
        $this->assertStringNotContainsString('pomodoro', strtolower($html));
    }

    public function test_send_report_now_api_dispatches_email()
    {
        Mail::fake();

        $response = $this->postJson('/api/tasks/send-report-now', [
            'email' => 'boss@corporation.com, lead@corporation.com',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                 ]);

        Mail::assertSent(WeeklyTaskReportMail::class, function ($mail) {
            return $mail->hasTo('boss@corporation.com') && $mail->hasTo('lead@corporation.com');
        });
    }

    public function test_artisan_command_sends_weekly_report()
    {
        Mail::fake();

        $this->artisan('report:weekly-tasks', ['--force' => true, '--email' => 'ceo@startup.vn'])
             ->assertExitCode(0);

        Mail::assertSent(WeeklyTaskReportMail::class, function ($mail) {
            return $mail->hasTo('ceo@startup.vn');
        });
    }
}
