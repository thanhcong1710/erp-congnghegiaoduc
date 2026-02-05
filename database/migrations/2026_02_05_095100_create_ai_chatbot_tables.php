<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAiChatbotTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Bảng quản lý quyền truy cập AI cho từng user
        Schema::create('ai_user_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->boolean('is_enabled')->default(false); // Bật/tắt quyền dùng AI

            // Hạn mức token
            $table->integer('daily_token_limit')->default(10000); // Giới hạn token/ngày
            $table->integer('monthly_token_limit')->default(300000); // Giới hạn token/tháng

            // Quyền sử dụng các tính năng
            $table->boolean('can_query_data')->default(true); // Tra cứu dữ liệu
            $table->boolean('can_generate_reports')->default(true); // Tạo báo cáo Excel
            $table->boolean('can_access_knowledge')->default(true); // Đọc tài liệu

            // Metadata
            $table->json('allowed_functions')->nullable(); // Danh sách function được phép gọi
            // VD: ["get_student_info", "get_revenue", "create_excel"]

            $table->timestamps();

            $table->unique('user_id');
        });

        // 2. Bảng lưu lịch sử chat (Conversation History) - TẠO TRƯỚC
        Schema::create('ai_conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Nhân viên nào đang chat
            $table->string('session_id')->index(); // Mỗi phiên chat có 1 session_id
            $table->string('title')->nullable(); // Tiêu đề tự động (VD: "Báo cáo doanh thu T1")
            $table->boolean('is_active')->default(true); // Đánh dấu phiên đang active
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // 3. Bảng theo dõi token usage (Tạo SAU ai_conversations)
        Schema::create('ai_token_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('conversation_id')->nullable()->constrained('ai_conversations')->onDelete('set null');

            $table->integer('input_tokens')->default(0); // Token input (prompt)
            $table->integer('output_tokens')->default(0); // Token output (response)
            $table->integer('total_tokens')->default(0); // Tổng

            $table->string('model_used')->nullable(); // VD: "gemini-1.5-flash"
            $table->decimal('estimated_cost', 10, 6)->default(0); // Chi phí ước tính (USD)

            $table->date('usage_date')->index(); // Ngày sử dụng (để tính daily limit)
            $table->timestamps();

            // Index để query nhanh
            $table->index(['user_id', 'usage_date']);
        });

        // 4. Bảng lưu từng tin nhắn (Messages)
        Schema::create('ai_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('ai_conversations')->onDelete('cascade');
            $table->enum('role', ['user', 'assistant', 'system']); // Ai nói
            $table->longText('content'); // Nội dung tin nhắn

            // Metadata: Lưu thông tin kỹ thuật (function call, token used...)
            $table->json('metadata')->nullable();
            // VD: {"function_called": "get_revenue", "tokens": 450, "execution_time": 1.2}

            $table->timestamps();
        });

        // 5. Bảng lưu Knowledge Base (Tài liệu hướng dẫn hệ thống)
        Schema::create('ai_knowledge_base', function (Blueprint $table) {
            $table->id();
            $table->string('category')->index(); // VD: "user_guide", "faq", "policy"
            $table->string('title'); // Tiêu đề (VD: "Cách tạo hợp đồng mới")
            $table->longText('content'); // Nội dung hướng dẫn
            $table->json('tags')->nullable(); // ["contract", "student", "enrollment"]
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 6. Bảng lưu các file Excel đã tạo (Generated Reports)
        Schema::create('ai_generated_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('ai_conversations')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('report_type'); // VD: "revenue_report", "student_list"
            $table->string('file_path'); // Đường dẫn file Excel
            $table->json('parameters')->nullable(); // Tham số đã dùng {"month": 1, "year": 2026}
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_generated_reports');
        Schema::dropIfExists('ai_token_usage');
        Schema::dropIfExists('ai_knowledge_base');
        Schema::dropIfExists('ai_messages');
        Schema::dropIfExists('ai_conversations');
        Schema::dropIfExists('ai_user_permissions');
    }
}
;
