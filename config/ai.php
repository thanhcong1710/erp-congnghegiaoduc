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
    'system_prompt' => "Bạn là một trợ lý AI thông minh và đa năng.

**Khả năng của bạn:**
1. Trả lời MỌI câu hỏi của người dùng về bất kỳ chủ đề nào (Lập trình, Marketing, Viết lách, Toán học, Đời sống...).
2. Có khả năng truy cập vào công cụ của hệ thống ERP để tra cứu dữ liệu khi người dùng hỏi về: Học viên, Doanh thu, Hợp đồng, Báo cáo...

**Nguyên tắc:**
- Nếu câu hỏi liên quan đến dữ liệu ERP -> HÃY DÙNG TOOLS (Functions) để lấy số liệu chính xác.
- Nếu câu hỏi là kiến thức chung (VD: Viết code PHP, Làm thơ, Dịch thuật...) -> Hãy trả lời nhiệt tình, chi tiết và chính xác nhất có thể. KHÔNG ĐƯỢC TỪ CHỐI.
- Luôn thân thiện, hữu ích và chuyên nghiệp.
- Trả lời bằng tiếng Việt.

**Phong cách:**
- Sử dụng format Markdown (bold, list, code block) để câu trả lời dễ đọc.
- Dùng emoji để tạo cảm giác thân thiện.",

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
            'description' => 'Lấy thông tin CHI TIẾT của 1 học viên cụ thể (khi biết chính xác tên)',
            'parameters' => [
                'student_name' => 'string|required',
            ],
        ],
        'search_students' => [
            'description' => 'Tìm kiếm học sinh theo từ khóa (tên, email, SĐT). Dùng khi cần tìm NHIỀU học sinh hoặc tìm tương đối',
            'parameters' => [
                'keyword' => 'string|required',
                'status' => 'string|nullable', // active, pending, reserve...
                'limit' => 'integer|nullable',
            ],
        ],
        'count_students_by_status' => [
            'description' => 'Đếm số lượng học sinh theo trạng thái (đang học, chưa học, bảo lưu...)',
            'parameters' => [
                'status' => 'string|nullable', // active, pending, reserve, all
                'branch_id' => 'integer|nullable',
            ],
        ],
        'get_student_statistics' => [
            'description' => 'Lấy thống kê tổng quan về học sinh (tổng số, mới, active, pending...)',
            'parameters' => [
                'branch_id' => 'integer|nullable',
                'from_date' => 'string|nullable',
                'to_date' => 'string|nullable',
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
        'get_tuition_fees_list' => [
            'description' => 'Lấy danh sách các gói phí (tuition fees). Tìm gói lẻ HOẶC gói combo chứa gói lẻ của sản phẩm',
            'parameters' => [
                'keyword' => 'string|nullable',
                'product_name' => 'string|nullable', // Tên sản phẩm (IELTS, TOEIC...)
                'status' => 'string|nullable', // active, inactive
                'limit' => 'integer|nullable',
            ],
        ],
        'get_tuition_fee_detail' => [
            'description' => 'Lấy thông tin chi tiết của 1 gói phí cụ thể',
            'parameters' => [
                'tuition_fee_id' => 'integer|required',
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
        'request_timeout' => 120, // Timeout (giây)
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
