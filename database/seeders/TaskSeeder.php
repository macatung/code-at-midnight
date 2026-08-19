<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use Carbon\Carbon;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        if (Task::count() === 0) {
            $today = Carbon::today()->toDateString();
            
            Task::create([
                'title' => 'Triển khai kiến trúc Multi-Agent AI cho dịch vụ chăm sóc khách hàng',
                'description' => 'Thiết kế orchestration flow giữa Agent phân loại và Agent giải quyết nghiệp vụ với latency < 500ms.',
                'status' => 'in_progress',
                'priority' => 'urgent',
                'category' => 'ai_agent',
                'estimated_pomodoros' => 4,
                'completed_pomodoros' => 2,
                'due_date' => $today,
            ]);

            Task::create([
                'title' => 'Tối ưu hóa chỉ mục Spatial Index cho PostgreSQL & PostGIS',
                'description' => 'Thêm GIST index cho cột geometry và tinh chỉnh query geo-fence để giảm thời gian phản hồi.',
                'status' => 'todo',
                'priority' => 'high',
                'category' => 'backend',
                'estimated_pomodoros' => 3,
                'completed_pomodoros' => 0,
                'due_date' => $today,
            ]);

            Task::create([
                'title' => 'Đồng bộ danh sách Kệ Pháp Cú với Desktop Mascot Companion',
                'description' => 'Kiểm thử khả năng phát chuông xoay 432Hz và hiển thị Kệ số ngẫu nhiên theo lịch trình.',
                'status' => 'done',
                'priority' => 'medium',
                'category' => 'mindful',
                'estimated_pomodoros' => 2,
                'completed_pomodoros' => 2,
                'due_date' => $today,
                'completed_at' => Carbon::now(),
            ]);

            Task::create([
                'title' => 'Thiết kế hệ thống Rate Limiter phân tán bằng Redis Cluster',
                'description' => 'Triển khai thuật toán Token Bucket chống ddos cho toàn bộ API Gateway.',
                'status' => 'todo',
                'priority' => 'medium',
                'category' => 'infra',
                'estimated_pomodoros' => 3,
                'completed_pomodoros' => 0,
                'due_date' => $today,
            ]);

            Task::create([
                'title' => 'Tọa thiền 15 phút xả stress và điều tức chánh niệm',
                'description' => 'Tạm ngắt kết nối màn hình, theo dõi hơi thở và tái tạo năng lượng tập trung.',
                'status' => 'todo',
                'priority' => 'low',
                'category' => 'mindful',
                'estimated_pomodoros' => 1,
                'completed_pomodoros' => 0,
                'due_date' => $today,
            ]);
        }
    }
}