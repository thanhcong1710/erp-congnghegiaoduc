<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Provider Configuration
    |--------------------------------------------------------------------------
    |
    | Cấu hình cho AI provider (Gemini, OpenAI...)
    |
    */

    'provider' => env('AI_PROVIDER', 'gemini'), // gemini | openai

    /*
    |--------------------------------------------------------------------------
    | Google Gemini API
    |--------------------------------------------------------------------------
    */
    'gemini' => [
        'api_key' => env('GEMINI_API_KEY', ''),
        'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'), // gemini-2.5-flash | gemini-2.5-pro
        'api_url' => 'https://generativelanguage.googleapis.com/v1beta',
        'max_tokens' => 8192,
        'temperature' => 0.7, // 0.0 - 1.0 (càng cao càng sáng tạo)
    ],

    /*
    |--------------------------------------------------------------------------
    | OpenAI API (Dự phòng)
    |--------------------------------------------------------------------------
    */
    'openai' => [
        'api_key' => env('OPENAI_API_KEY', ''),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
        'api_url' => 'https://api.openai.com/v1',
        'max_tokens' => 4096,
        'temperature' => 0.7,
    ],

    /*
    |--------------------------------------------------------------------------
    | System Prompt (Tính cách AI)
    |--------------------------------------------------------------------------
    */
    'system_prompt' => "Bạn là trợ lý AI thông minh của hệ thống ERP giáo dục.

**Nhiệm vụ:**
- Trả lời câu hỏi về hệ thống ERP (học viên, hợp đồng, doanh thu...)
- Tra cứu dữ liệu từ database khi được yêu cầu
- Tạo báo cáo Excel khi cần
- Hướng dẫn nhân viên sử dụng hệ thống

**Nguyên tắc:**
1. Chỉ trả lời câu hỏi liên quan đến hệ thống ERP
2. Nếu câu hỏi không liên quan (thời tiết, tin tức...), từ chối lịch sự
3. Luôn hỏi lại nếu thiếu thông tin (VD: tháng nào? chi nhánh nào?)
4. Trả lời ngắn gọn, súc tích, dùng tiếng Việt
5. Nếu cần gọi function, hãy gọi đúng function với tham số chính xác

**Phong cách:**
- Thân thiện, chuyên nghiệp
- Dùng emoji phù hợp (✅ ❌ 📊 💰)
- Trả lời theo format dễ đọc (bullet points, bảng...)",

    /*
    |--------------------------------------------------------------------------
    | Available Functions (Tools)
    |--------------------------------------------------------------------------
    |
    | Danh sách các function AI có thể gọi
    |
    */
    'functions' => [
        'get_student_info' => [
            'description' => 'Lấy thông tin học viên (điểm, chuyên cần, hợp đồng)',
            'parameters' => [
                'student_name' => 'string|required',
            ],
        ],
        'get_revenue_report' => [
            'description' => 'Lấy báo cáo doanh thu theo tháng/năm',
            'parameters' => [
                'month' => 'integer|required',
                'year' => 'integer|required',
                'branch_id' => 'integer|nullable',
            ],
        ],
        'search_knowledge' => [
            'description' => 'Tìm kiếm tài liệu hướng dẫn trong knowledge base',
            'parameters' => [
                'query' => 'string|required',
            ],
        ],
        'create_excel_report' => [
            'description' => 'Tạo file Excel báo cáo',
            'parameters' => [
                'report_type' => 'string|required', // student_list, revenue, contract...
                'filters' => 'array|nullable',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Limits & Quotas
    |--------------------------------------------------------------------------
    */
    'limits' => [
        'max_context_messages' => 10, // Số tin nhắn lịch sử gửi cho AI
        'max_knowledge_results' => 3, // Số tài liệu tìm thấy gửi cho AI
        'request_timeout' => 30, // Timeout (giây)
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    */
    'logging' => [
        'enabled' => env('AI_LOGGING_ENABLED', true),
        'channel' => 'daily', // Ghi log vào storage/logs
    ],
];
