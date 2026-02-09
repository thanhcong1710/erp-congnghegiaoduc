# 🤖 KẾ HOẠCH TRIỂN KHAI: AI TUTOR 1-1 TỰ ĐỘNG

**Dự án:** Hệ thống AI giảng dạy 1-1 tự động  
**Ngày:** 09/02/2026  
**Phiên bản:** 1.0

---

## 📑 MỤC LỤC

1. [Tổng quan hệ thống](#1-tổng-quan-hệ-thống)
2. [Kiến trúc hệ thống](#2-kiến-trúc-hệ-thống)
3. [Quy trình hoạt động](#3-quy-trình-hoạt-động)
4. [Tech Stack](#4-tech-stack)
5. [Database Schema](#5-database-schema)
6. [API Design](#6-api-design)
7. [AI Engine](#7-ai-engine)
8. [Roadmap triển khai](#8-roadmap-triển-khai)
9. [Chi phí ước tính](#9-chi-phí-ước-tính)
10. [Demo & Testing](#10-demo--testing)

---

## 1. TỔNG QUAN HỆ THỐNG

### 1.1 Bài toán cần giải quyết

**User Story:**
> "Là một giáo viên, tôi muốn upload bài học (PDF, PPT, hoặc text), và AI sẽ tự động giảng dạy 1-1 cho từng học sinh, giải thích bằng giọng nói, trả lời câu hỏi, và đánh giá hiểu bài."

### 1.2 Tính năng chính

#### **Cho Giáo viên:**
1. ✅ Upload bài học (PDF, DOCX, PPT, hoặc nhập text)
2. ✅ Cấu hình bài học:
   - Môn học (Tiếng Anh, Toán, Logic...)
   - Độ tuổi/Trình độ
   - Mục tiêu học tập
   - Thời lượng (15-60 phút)
3. ✅ Xem báo cáo:
   - Học sinh nào đã học
   - Điểm số, thời gian học
   - Câu hỏi khó, điểm yếu

#### **Cho Học sinh:**
1. ✅ Chọn bài học được giao
2. ✅ Học 1-1 với AI:
   - AI giảng bài bằng giọng nói (Text-to-Speech)
   - Học sinh nghe và đọc (có transcript)
   - AI đặt câu hỏi kiểm tra
   - Học sinh trả lời (text hoặc voice)
   - AI giải thích nếu sai
3. ✅ Xem kết quả:
   - Điểm số
   - Thời gian học
   - Điểm mạnh/yếu

### 1.3 User Flow

```
┌─────────────────────────────────────────────────────────────┐
│                      GIÁO VIÊN                              │
└─────────────────────────────────────────────────────────────┘
                           │
                           ▼
        ┌──────────────────────────────────────┐
        │  1. Upload bài học (PDF/DOCX/Text)   │
        └──────────────────────────────────────┘
                           │
                           ▼
        ┌──────────────────────────────────────┐
        │  2. AI xử lý & tạo nội dung giảng    │
        │     - Extract text                   │
        │     - Phân tích nội dung             │
        │     - Tạo câu hỏi                    │
        │     - Tạo script giảng dạy           │
        └──────────────────────────────────────┘
                           │
                           ▼
        ┌──────────────────────────────────────┐
        │  3. Giao bài cho học sinh            │
        └──────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                      HỌC SINH                               │
└─────────────────────────────────────────────────────────────┘
                           │
                           ▼
        ┌──────────────────────────────────────┐
        │  4. Bắt đầu học với AI               │
        └──────────────────────────────────────┘
                           │
                           ▼
        ┌──────────────────────────────────────┐
        │  5. AI giảng bài (Text-to-Speech)    │
        │     - Giới thiệu bài học             │
        │     - Giải thích từng phần           │
        │     - Cho ví dụ                      │
        └──────────────────────────────────────┘
                           │
                           ▼
        ┌──────────────────────────────────────┐
        │  6. AI đặt câu hỏi                   │
        │     - Multiple choice                │
        │     - True/False                     │
        │     - Short answer                   │
        └──────────────────────────────────────┘
                           │
                           ▼
        ┌──────────────────────────────────────┐
        │  7. Học sinh trả lời                 │
        │     - Text input                     │
        │     - Voice input (optional)         │
        └──────────────────────────────────────┘
                           │
                           ▼
        ┌──────────────────────────────────────┐
        │  8. AI đánh giá & feedback           │
        │     - Đúng → Khen, tiếp tục          │
        │     - Sai → Giải thích, cho thử lại  │
        └──────────────────────────────────────┘
                           │
                           ▼
        ┌──────────────────────────────────────┐
        │  9. Kết thúc → Báo cáo kết quả       │
        │     - Điểm số                        │
        │     - Thời gian                      │
        │     - Điểm mạnh/yếu                  │
        └──────────────────────────────────────┘
```

---

## 2. KIẾN TRÚC HỆ THỐNG

### 2.1 High-level Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                     CLIENT LAYER                            │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐    │
│  │   Web App    │  │  Mobile App  │  │  Admin Panel │    │
│  │  (Next.js)   │  │(React Native)│  │  (Laravel)   │    │
│  └──────────────┘  └──────────────┘  └──────────────┘    │
│                                                             │
└─────────────────────────────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                      API GATEWAY                            │
│                   (Laravel API + Redis)                     │
└─────────────────────────────────────────────────────────────┘
                           │
          ┌────────────────┼────────────────┐
          ▼                ▼                ▼
┌──────────────┐  ┌──────────────┐  ┌──────────────┐
│   Content    │  │  AI Engine   │  │  Analytics   │
│   Service    │  │   Service    │  │   Service    │
│              │  │              │  │              │
│ - Upload     │  │ - LLM API    │  │ - Tracking   │
│ - Extract    │  │ - TTS API    │  │ - Reporting  │
│ - Store      │  │ - STT API    │  │ - Insights   │
└──────────────┘  └──────────────┘  └──────────────┘
          │                │                │
          └────────────────┼────────────────┘
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                     DATA LAYER                              │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐    │
│  │    MySQL     │  │    Redis     │  │      S3      │    │
│  │ (Structured) │  │   (Cache)    │  │   (Files)    │    │
│  └──────────────┘  └──────────────┘  └──────────────┘    │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### 2.2 Component Details

#### **1. Content Service**
**Chức năng:**
- Upload file (PDF, DOCX, PPT)
- Extract text từ file
- Lưu trữ file trên S3
- Quản lý metadata

**Tech:**
- Laravel Storage
- AWS S3 / MinIO
- PDF Parser (smalot/pdfparser)
- DOCX Parser (phpoffice/phpword)
- PPT Parser (phpoffice/phppresentation)

---

#### **2. AI Engine Service**
**Chức năng:**
- Phân tích nội dung bài học
- Tạo script giảng dạy
- Tạo câu hỏi tự động
- Đánh giá câu trả lời
- Text-to-Speech
- Speech-to-Text (optional)

**Tech:**
- OpenAI GPT-4o / Claude 3.5 Sonnet
- Google Text-to-Speech / ElevenLabs
- Whisper API (for STT)

---

#### **3. Analytics Service**
**Chức năng:**
- Tracking học tập
- Tính điểm
- Phân tích điểm mạnh/yếu
- Báo cáo cho giáo viên

**Tech:**
- Laravel Events
- MySQL
- Redis (real-time)

---

## 3. QUY TRÌNH HOẠT ĐỘNG

### 3.1 Phase 1: Giáo viên upload bài học

#### **Step 1: Upload file**

**API Request:**
```http
POST /api/lessons/upload
Content-Type: multipart/form-data

{
  "file": <PDF/DOCX/PPT>,
  "title": "Bài 1: Present Simple Tense",
  "subject": "english",
  "grade_level": "grade_6",
  "duration": 30,
  "learning_objectives": "Học sinh hiểu và sử dụng được thì hiện tại đơn"
}
```

**Backend Process:**
1. Validate file (type, size < 10MB)
2. Upload to S3
3. Extract text từ file
4. Save metadata to database
5. Queue job: Process lesson content

---

#### **Step 2: AI xử lý nội dung**

**AI Prompt (GPT-4):**
```
Bạn là một giáo viên {subject} giàu kinh nghiệm. 
Hãy phân tích nội dung bài học sau và tạo:

1. **Lesson Plan** (Kế hoạch giảng dạy):
   - Introduction (2 phút): Giới thiệu bài học
   - Main Content (20 phút): Giải thích chi tiết
   - Practice (5 phút): Bài tập thực hành
   - Summary (3 phút): Tóm tắt

2. **Teaching Script** (Kịch bản giảng):
   - Chia thành các đoạn ngắn (mỗi đoạn 1-2 phút)
   - Giọng điệu thân thiện, dễ hiểu
   - Có ví dụ cụ thể

3. **Questions** (Câu hỏi kiểm tra):
   - 5-10 câu hỏi
   - Nhiều dạng: Multiple choice, True/False, Short answer
   - Có đáp án và giải thích

Nội dung bài học:
---
{lesson_content}
---

Trả về JSON format:
{
  "lesson_plan": {...},
  "teaching_script": [...],
  "questions": [...]
}
```

**Output Example:**
```json
{
  "lesson_plan": {
    "introduction": {
      "duration": 2,
      "content": "Chào em! Hôm nay chúng ta sẽ học về thì hiện tại đơn..."
    },
    "main_content": {
      "duration": 20,
      "sections": [
        {
          "title": "Công thức",
          "content": "Thì hiện tại đơn có công thức: S + V(s/es)..."
        },
        {
          "title": "Cách dùng",
          "content": "Chúng ta dùng thì hiện tại đơn để..."
        }
      ]
    },
    "practice": {
      "duration": 5,
      "content": "Bây giờ chúng ta sẽ làm một số bài tập..."
    },
    "summary": {
      "duration": 3,
      "content": "Tóm lại, thì hiện tại đơn được dùng để..."
    }
  },
  "teaching_script": [
    {
      "order": 1,
      "type": "introduction",
      "text": "Chào em! Hôm nay chúng ta sẽ học về thì hiện tại đơn. Đây là một trong những thì quan trọng nhất trong tiếng Anh đấy!",
      "duration": 15
    },
    {
      "order": 2,
      "type": "explanation",
      "text": "Thì hiện tại đơn có công thức rất đơn giản: Chủ ngữ + Động từ. Ví dụ: I play football. She plays football.",
      "duration": 30
    }
  ],
  "questions": [
    {
      "id": 1,
      "type": "multiple_choice",
      "question": "Câu nào sau đây đúng?",
      "options": [
        "I plays football",
        "I play football",
        "I playing football"
      ],
      "correct_answer": "I play football",
      "explanation": "Với chủ ngữ 'I', động từ không thêm 's/es'"
    },
    {
      "id": 2,
      "type": "true_false",
      "question": "She go to school every day. (Đúng/Sai)",
      "correct_answer": false,
      "explanation": "Sai! Phải là 'She goes to school' vì chủ ngữ là ngôi thứ 3 số ít"
    }
  ]
}
```

---

### 3.2 Phase 2: Học sinh học với AI

#### **Step 1: Bắt đầu session**

**API Request:**
```http
POST /api/learning-sessions/start
{
  "lesson_id": 123,
  "student_id": 456
}
```

**Response:**
```json
{
  "session_id": "sess_abc123",
  "lesson": {
    "id": 123,
    "title": "Bài 1: Present Simple Tense",
    "duration": 30
  },
  "first_segment": {
    "order": 1,
    "text": "Chào em! Hôm nay chúng ta sẽ học về thì hiện tại đơn...",
    "audio_url": "https://s3.../audio_1.mp3"
  }
}
```

---

#### **Step 2: AI giảng bài (Text-to-Speech)**

**Process:**
1. Lấy teaching_script từ database
2. Convert text → speech (TTS)
3. Cache audio file trên S3
4. Stream audio cho học sinh

**TTS Implementation:**

**Option 1: Google Text-to-Speech (Rẻ, chất lượng OK)**
```php
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;

function textToSpeech($text, $languageCode = 'vi-VN') {
    $client = new TextToSpeechClient();
    
    $input = new SynthesisInput();
    $input->setText($text);
    
    $voice = new VoiceSelectionParams();
    $voice->setLanguageCode($languageCode);
    $voice->setName('vi-VN-Wavenet-A'); // Giọng nữ tự nhiên
    
    $audioConfig = new AudioConfig();
    $audioConfig->setAudioEncoding(AudioEncoding::MP3);
    $audioConfig->setSpeakingRate(0.9); // Nói chậm hơn một chút
    $audioConfig->setPitch(0.0);
    
    $response = $client->synthesizeSpeech($input, $voice, $audioConfig);
    $audioContent = $response->getAudioContent();
    
    // Save to S3
    $filename = 'audio_' . md5($text) . '.mp3';
    Storage::disk('s3')->put($filename, $audioContent);
    
    return Storage::disk('s3')->url($filename);
}
```

**Option 2: ElevenLabs (Đắt hơn, chất lượng tốt hơn)**
```php
use GuzzleHttp\Client;

function textToSpeechElevenLabs($text) {
    $client = new Client();
    
    $response = $client->post('https://api.elevenlabs.io/v1/text-to-speech/21m00Tcm4TlvDq8ikWAM', [
        'headers' => [
            'xi-api-key' => env('ELEVENLABS_API_KEY'),
            'Content-Type' => 'application/json',
        ],
        'json' => [
            'text' => $text,
            'model_id' => 'eleven_multilingual_v2',
            'voice_settings' => [
                'stability' => 0.5,
                'similarity_boost' => 0.75,
            ],
        ],
    ]);
    
    $audioContent = $response->getBody()->getContents();
    
    // Save to S3
    $filename = 'audio_' . md5($text) . '.mp3';
    Storage::disk('s3')->put($filename, $audioContent);
    
    return Storage::disk('s3')->url($filename);
}
```

---

#### **Step 3: AI đặt câu hỏi**

**API Request:**
```http
GET /api/learning-sessions/{session_id}/next-question
```

**Response:**
```json
{
  "question": {
    "id": 1,
    "type": "multiple_choice",
    "question": "Câu nào sau đây đúng?",
    "question_audio_url": "https://s3.../question_1.mp3",
    "options": [
      {
        "id": "a",
        "text": "I plays football",
        "audio_url": "https://s3.../option_1a.mp3"
      },
      {
        "id": "b",
        "text": "I play football",
        "audio_url": "https://s3.../option_1b.mp3"
      },
      {
        "id": "c",
        "text": "I playing football",
        "audio_url": "https://s3.../option_1c.mp3"
      }
    ]
  }
}
```

---

#### **Step 4: Học sinh trả lời**

**API Request:**
```http
POST /api/learning-sessions/{session_id}/answer
{
  "question_id": 1,
  "answer": "b"
}
```

**Backend Process:**
1. Kiểm tra đáp án
2. Tính điểm
3. Tạo feedback

**Response (Đúng):**
```json
{
  "is_correct": true,
  "feedback": {
    "text": "Chính xác! 'I play football' là đúng vì với chủ ngữ 'I', động từ không thêm 's/es'.",
    "audio_url": "https://s3.../feedback_correct_1.mp3"
  },
  "score": 10,
  "next_action": "continue"
}
```

**Response (Sai):**
```json
{
  "is_correct": false,
  "feedback": {
    "text": "Chưa đúng em nhé! Với chủ ngữ 'I', động từ không thêm 's/es'. Vậy câu đúng là 'I play football'. Em thử lại nhé!",
    "audio_url": "https://s3.../feedback_wrong_1.mp3"
  },
  "score": 0,
  "next_action": "retry"
}
```

---

#### **Step 5: Kết thúc & Báo cáo**

**API Request:**
```http
POST /api/learning-sessions/{session_id}/complete
```

**Response:**
```json
{
  "session_id": "sess_abc123",
  "result": {
    "total_questions": 10,
    "correct_answers": 8,
    "score": 80,
    "time_spent": 25,
    "strengths": [
      "Hiểu rõ công thức thì hiện tại đơn",
      "Làm tốt bài tập về động từ"
    ],
    "weaknesses": [
      "Còn nhầm lẫn khi thêm s/es với ngôi thứ 3"
    ],
    "recommendations": [
      "Ôn lại phần thêm s/es",
      "Làm thêm bài tập về ngôi thứ 3"
    ]
  },
  "certificate": {
    "url": "https://s3.../certificate_123.pdf",
    "message": "Chúc mừng em đã hoàn thành bài học!"
  }
}
```

---

## 4. TECH STACK

### 4.1 Backend

| Component | Technology | Lý do chọn |
|-----------|------------|------------|
| **API Framework** | Laravel 11 | - Có sẵn ERP<br>- Ecosystem mạnh<br>- Easy to scale |
| **Database** | MySQL 8.0 | - Reliable<br>- Có sẵn<br>- Good for structured data |
| **Cache** | Redis | - Fast<br>- Session storage<br>- Queue |
| **Queue** | Laravel Queue + Redis | - Background jobs<br>- AI processing |
| **Storage** | AWS S3 / MinIO | - Scalable<br>- CDN ready<br>- Cost-effective |

### 4.2 AI Services

| Service | Provider | Cost | Use Case |
|---------|----------|------|----------|
| **LLM** | OpenAI GPT-4o | $5/1M tokens | - Phân tích nội dung<br>- Tạo câu hỏi<br>- Đánh giá câu trả lời |
| **LLM (Alternative)** | Claude 3.5 Sonnet | $3/1M tokens | - Backup cho GPT-4<br>- Tốt cho giải thích |
| **Text-to-Speech** | Google TTS | $4/1M chars | - Giá rẻ<br>- Chất lượng OK<br>- Hỗ trợ tiếng Việt |
| **TTS (Premium)** | ElevenLabs | $22/100K chars | - Chất lượng cao<br>- Giọng tự nhiên |
| **Speech-to-Text** | Whisper API | $0.006/min | - Học sinh trả lời bằng voice |

### 4.3 Frontend

| Component | Technology |
|-----------|------------|
| **Web App** | Next.js 14 (React) |
| **Mobile App** | React Native |
| **UI Library** | Tailwind CSS + shadcn/ui |
| **State Management** | Zustand / React Query |
| **Audio Player** | Howler.js |

### 4.4 DevOps

| Component | Technology |
|-----------|------------|
| **Hosting** | AWS EC2 / DigitalOcean |
| **CI/CD** | GitHub Actions |
| **Monitoring** | Sentry + DataDog |
| **Analytics** | Mixpanel |

---

## 5. DATABASE SCHEMA

### 5.1 Core Tables

```sql
-- Bảng bài học
CREATE TABLE lessons (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    teacher_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    subject VARCHAR(50) NOT NULL, -- english, math, logic
    grade_level VARCHAR(50), -- grade_1, grade_2, ...
    duration INT NOT NULL, -- phút
    learning_objectives TEXT,
    
    -- File gốc
    original_file_url VARCHAR(500),
    original_file_type VARCHAR(20), -- pdf, docx, ppt
    
    -- Nội dung đã extract
    extracted_content LONGTEXT,
    
    -- AI generated content
    lesson_plan JSON,
    teaching_script JSON,
    questions JSON,
    
    -- Metadata
    status VARCHAR(20) DEFAULT 'draft', -- draft, processing, ready, error
    processed_at TIMESTAMP NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_teacher (teacher_id),
    INDEX idx_subject (subject),
    INDEX idx_status (status)
);

-- Bảng script giảng dạy (chi tiết)
CREATE TABLE lesson_segments (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    lesson_id BIGINT UNSIGNED NOT NULL,
    order_index INT NOT NULL,
    type VARCHAR(50), -- introduction, explanation, example, practice, summary
    text TEXT NOT NULL,
    duration INT, -- giây
    
    -- Audio
    audio_url VARCHAR(500),
    audio_generated_at TIMESTAMP NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE,
    INDEX idx_lesson_order (lesson_id, order_index)
);

-- Bảng câu hỏi
CREATE TABLE lesson_questions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    lesson_id BIGINT UNSIGNED NOT NULL,
    order_index INT NOT NULL,
    type VARCHAR(50), -- multiple_choice, true_false, short_answer, essay
    question TEXT NOT NULL,
    options JSON, -- cho multiple choice
    correct_answer TEXT NOT NULL,
    explanation TEXT,
    
    -- Audio
    question_audio_url VARCHAR(500),
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE,
    INDEX idx_lesson_order (lesson_id, order_index)
);

-- Bảng phiên học
CREATE TABLE learning_sessions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    session_code VARCHAR(50) UNIQUE NOT NULL,
    lesson_id BIGINT UNSIGNED NOT NULL,
    student_id BIGINT UNSIGNED NOT NULL,
    
    -- Progress
    current_segment_id BIGINT UNSIGNED,
    current_question_index INT DEFAULT 0,
    
    -- Results
    total_questions INT DEFAULT 0,
    correct_answers INT DEFAULT 0,
    score DECIMAL(5,2) DEFAULT 0,
    
    -- Time tracking
    started_at TIMESTAMP NOT NULL,
    completed_at TIMESTAMP NULL,
    time_spent INT, -- giây
    
    -- Status
    status VARCHAR(20) DEFAULT 'in_progress', -- in_progress, completed, abandoned
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (lesson_id) REFERENCES lessons(id),
    INDEX idx_student (student_id),
    INDEX idx_status (status)
);

-- Bảng câu trả lời của học sinh
CREATE TABLE student_answers (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    session_id BIGINT UNSIGNED NOT NULL,
    question_id BIGINT UNSIGNED NOT NULL,
    
    -- Answer
    answer TEXT NOT NULL,
    is_correct BOOLEAN NOT NULL,
    
    -- Feedback
    feedback TEXT,
    feedback_audio_url VARCHAR(500),
    
    -- Time
    answered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    time_spent INT, -- giây
    
    FOREIGN KEY (session_id) REFERENCES learning_sessions(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES lesson_questions(id),
    INDEX idx_session (session_id)
);

-- Bảng phân tích học tập
CREATE TABLE learning_analytics (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    session_id BIGINT UNSIGNED NOT NULL,
    student_id BIGINT UNSIGNED NOT NULL,
    lesson_id BIGINT UNSIGNED NOT NULL,
    
    -- Strengths & Weaknesses
    strengths JSON,
    weaknesses JSON,
    recommendations JSON,
    
    -- Detailed metrics
    metrics JSON, -- {topic_1: 80%, topic_2: 60%, ...}
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (session_id) REFERENCES learning_sessions(id) ON DELETE CASCADE,
    INDEX idx_student (student_id),
    INDEX idx_lesson (lesson_id)
);
```

---

## 6. API DESIGN

### 6.1 Lesson Management APIs

#### **1. Upload Lesson**
```http
POST /api/lessons/upload
Authorization: Bearer {teacher_token}
Content-Type: multipart/form-data

Body:
- file: <PDF/DOCX/PPT>
- title: string
- subject: string (english|math|logic)
- grade_level: string
- duration: integer (minutes)
- learning_objectives: string

Response 200:
{
  "lesson_id": 123,
  "status": "processing",
  "message": "Bài học đang được xử lý. Vui lòng chờ 2-3 phút."
}
```

#### **2. Get Lesson Status**
```http
GET /api/lessons/{lesson_id}
Authorization: Bearer {teacher_token}

Response 200:
{
  "id": 123,
  "title": "Bài 1: Present Simple Tense",
  "status": "ready",
  "lesson_plan": {...},
  "total_segments": 15,
  "total_questions": 10,
  "processed_at": "2026-02-09T11:30:00Z"
}
```

#### **3. Assign Lesson to Students**
```http
POST /api/lessons/{lesson_id}/assign
Authorization: Bearer {teacher_token}

Body:
{
  "student_ids": [456, 789, 101],
  "due_date": "2026-02-15"
}

Response 200:
{
  "assigned_count": 3,
  "message": "Đã giao bài cho 3 học sinh"
}
```

---

### 6.2 Learning Session APIs

#### **1. Start Session**
```http
POST /api/learning-sessions/start
Authorization: Bearer {student_token}

Body:
{
  "lesson_id": 123
}

Response 200:
{
  "session_id": "sess_abc123",
  "lesson": {
    "id": 123,
    "title": "Bài 1: Present Simple Tense",
    "duration": 30
  },
  "first_segment": {
    "id": 1,
    "text": "Chào em! Hôm nay chúng ta sẽ học về...",
    "audio_url": "https://s3.../audio_1.mp3",
    "duration": 15
  }
}
```

#### **2. Get Next Segment**
```http
GET /api/learning-sessions/{session_id}/next-segment
Authorization: Bearer {student_token}

Response 200:
{
  "segment": {
    "id": 2,
    "type": "explanation",
    "text": "Thì hiện tại đơn có công thức...",
    "audio_url": "https://s3.../audio_2.mp3",
    "duration": 30
  },
  "progress": {
    "current": 2,
    "total": 15,
    "percentage": 13
  }
}
```

#### **3. Get Next Question**
```http
GET /api/learning-sessions/{session_id}/next-question
Authorization: Bearer {student_token}

Response 200:
{
  "question": {
    "id": 1,
    "type": "multiple_choice",
    "question": "Câu nào sau đây đúng?",
    "question_audio_url": "https://s3.../question_1.mp3",
    "options": [...]
  },
  "progress": {
    "current": 1,
    "total": 10
  }
}
```

#### **4. Submit Answer**
```http
POST /api/learning-sessions/{session_id}/answer
Authorization: Bearer {student_token}

Body:
{
  "question_id": 1,
  "answer": "b"
}

Response 200:
{
  "is_correct": true,
  "feedback": {
    "text": "Chính xác! ...",
    "audio_url": "https://s3.../feedback_1.mp3"
  },
  "score": 10,
  "next_action": "continue"
}
```

#### **5. Complete Session**
```http
POST /api/learning-sessions/{session_id}/complete
Authorization: Bearer {student_token}

Response 200:
{
  "session_id": "sess_abc123",
  "result": {
    "total_questions": 10,
    "correct_answers": 8,
    "score": 80,
    "time_spent": 1500,
    "strengths": [...],
    "weaknesses": [...],
    "recommendations": [...]
  }
}
```

---

## 7. AI ENGINE

### 7.1 Content Processing Pipeline

```
Upload File
    │
    ▼
Extract Text
    │
    ▼
Analyze Content (GPT-4)
    │
    ├─► Create Lesson Plan
    ├─► Generate Teaching Script
    └─► Generate Questions
    │
    ▼
Generate Audio (TTS)
    │
    ▼
Save to Database
    │
    ▼
Ready for Students
```

### 7.2 AI Prompts

#### **Prompt 1: Phân tích nội dung & tạo lesson plan**

```
System: Bạn là một giáo viên {subject} chuyên nghiệp với 10 năm kinh nghiệm.

User: Hãy phân tích nội dung bài học sau và tạo:

1. **Lesson Plan** với cấu trúc:
   - Introduction (10% thời gian): Giới thiệu bài học, tạo hứng thú
   - Main Content (60% thời gian): Giải thích chi tiết, chia thành 3-5 sections
   - Practice (20% thời gian): Bài tập thực hành
   - Summary (10% thời gian): Tóm tắt, nhấn mạnh điểm chính

2. **Teaching Script**: 
   - Chia thành các đoạn ngắn (mỗi đoạn 15-30 giây)
   - Giọng điệu thân thiện, dễ hiểu, phù hợp với {grade_level}
   - Có ví dụ cụ thể, gần gũi với cuộc sống
   - Sử dụng câu hỏi tu từ để tạo tương tác

3. **Questions** (10 câu):
   - 5 câu multiple choice (4 options)
   - 3 câu true/false
   - 2 câu short answer
   - Độ khó tăng dần
   - Có giải thích chi tiết cho mỗi câu

Nội dung bài học:
---
{extracted_content}
---

Thông tin bổ sung:
- Môn học: {subject}
- Trình độ: {grade_level}
- Thời lượng: {duration} phút
- Mục tiêu: {learning_objectives}

Trả về JSON format theo schema:
{
  "lesson_plan": {
    "introduction": {"duration": number, "content": string},
    "main_content": {
      "duration": number,
      "sections": [
        {"title": string, "content": string, "examples": [string]}
      ]
    },
    "practice": {"duration": number, "content": string},
    "summary": {"duration": number, "content": string}
  },
  "teaching_script": [
    {
      "order": number,
      "type": string,
      "text": string,
      "duration": number
    }
  ],
  "questions": [
    {
      "id": number,
      "type": string,
      "question": string,
      "options": [string] | null,
      "correct_answer": string,
      "explanation": string
    }
  ]
}
```

---

#### **Prompt 2: Đánh giá câu trả lời**

```
System: Bạn là một giáo viên {subject} đang đánh giá câu trả lời của học sinh.

User: Hãy đánh giá câu trả lời sau:

Câu hỏi: {question}
Đáp án đúng: {correct_answer}
Câu trả lời của học sinh: {student_answer}

Yêu cầu:
1. Xác định câu trả lời đúng hay sai
2. Nếu đúng: Khen ngợi, khuyến khích
3. Nếu sai: 
   - Giải thích tại sao sai
   - Hướng dẫn cách làm đúng
   - Cho học sinh thử lại
4. Giọng điệu thân thiện, động viên

Trả về JSON:
{
  "is_correct": boolean,
  "feedback": string,
  "encouragement": string
}
```

---

#### **Prompt 3: Phân tích kết quả học tập**

```
System: Bạn là một chuyên gia phân tích học tập.

User: Hãy phân tích kết quả học tập của học sinh:

Tổng số câu hỏi: {total_questions}
Số câu đúng: {correct_answers}
Chi tiết câu trả lời:
{answers_detail}

Yêu cầu:
1. Xác định điểm mạnh (strengths)
2. Xác định điểm yếu (weaknesses)
3. Đưa ra gợi ý cải thiện (recommendations)

Trả về JSON:
{
  "strengths": [string],
  "weaknesses": [string],
  "recommendations": [string]
}
```

---

### 7.3 Cost Estimation (AI APIs)

#### **Scenario: 1 bài học, 1 học sinh**

| Step | Service | Input | Cost |
|------|---------|-------|------|
| **1. Process Lesson** | GPT-4o | 2,000 tokens | $0.01 |
| **2. Generate Audio (15 segments)** | Google TTS | 3,000 chars | $0.012 |
| **3. Generate Questions Audio (10)** | Google TTS | 1,000 chars | $0.004 |
| **4. Evaluate Answers (10)** | GPT-4o | 500 tokens | $0.0025 |
| **5. Generate Feedback Audio (10)** | Google TTS | 1,500 chars | $0.006 |
| **6. Analyze Results** | GPT-4o | 300 tokens | $0.0015 |
| **Total per student** | | | **$0.036** |

#### **Monthly Cost (1,000 students)**
- 1,000 students × $0.036 = **$36/month**
- Rất rẻ! 💰

---

## 8. ROADMAP TRIỂN KHAI

### 8.1 Phase 1: MVP (Tháng 1-2) - 8 tuần

#### **Week 1-2: Setup & Infrastructure**
- [ ] Setup Laravel project
- [ ] Database schema
- [ ] AWS S3 / MinIO setup
- [ ] Redis setup
- [ ] OpenAI API integration
- [ ] Google TTS integration

#### **Week 3-4: Content Processing**
- [ ] File upload API
- [ ] PDF/DOCX/PPT parser
- [ ] AI content analysis (GPT-4)
- [ ] Lesson plan generation
- [ ] Teaching script generation
- [ ] Questions generation
- [ ] Background job processing

#### **Week 5-6: Learning Session**
- [ ] Session management API
- [ ] Audio generation (TTS)
- [ ] Audio caching
- [ ] Question delivery
- [ ] Answer evaluation
- [ ] Feedback generation

#### **Week 7-8: Frontend & Testing**
- [ ] Teacher dashboard (upload, view lessons)
- [ ] Student learning interface
- [ ] Audio player
- [ ] Progress tracking
- [ ] Testing với 10 bài học mẫu
- [ ] Bug fixes

**Deliverable:** MVP có thể demo với 1 bài học hoàn chỉnh

---

### 8.2 Phase 2: Beta (Tháng 3-4) - 8 tuần

#### **Week 9-10: Advanced Features**
- [ ] Speech-to-Text (học sinh trả lời bằng voice)
- [ ] Multi-language support (English, Vietnamese)
- [ ] Adaptive difficulty
- [ ] Gamification (points, badges, streak)

#### **Week 11-12: Analytics & Reporting**
- [ ] Learning analytics
- [ ] Teacher dashboard (student progress)
- [ ] Student dashboard (my progress)
- [ ] Export reports (PDF, Excel)

#### **Week 13-14: Mobile App**
- [ ] React Native setup
- [ ] Core features (learning session)
- [ ] Offline mode
- [ ] Push notifications

#### **Week 15-16: Beta Testing**
- [ ] Pilot với 5 giáo viên, 50 học sinh
- [ ] Collect feedback
- [ ] Iterate & improve
- [ ] Performance optimization

**Deliverable:** Beta version sẵn sàng cho 100 users

---

### 8.3 Phase 3: Launch (Tháng 5-6) - 8 tuần

#### **Week 17-18: Polish & Optimization**
- [ ] UI/UX improvements
- [ ] Performance tuning
- [ ] Security audit
- [ ] Load testing

#### **Week 19-20: Content Library**
- [ ] Tạo 50 bài học mẫu (Tiếng Anh, Toán)
- [ ] Quality check
- [ ] Teacher training materials

#### **Week 21-22: Marketing & Launch**
- [ ] Landing page
- [ ] Demo videos
- [ ] PR & Media
- [ ] Launch event

#### **Week 23-24: Post-launch Support**
- [ ] Monitor & fix bugs
- [ ] User support
- [ ] Collect feedback
- [ ] Plan next features

**Deliverable:** Public launch, 500+ users

---

## 9. CHI PHÍ ƯỚC TÍNH

### 9.1 Development Cost (6 tháng)

| Role | Số lượng | Lương/tháng | Tổng (6 tháng) |
|------|----------|-------------|----------------|
| **Full-stack Developer** | 2 | 40M | 480M |
| **AI Engineer** | 1 | 50M | 300M |
| **Mobile Developer** | 1 | 40M | 240M |
| **Product Designer** | 1 | 30M | 180M |
| **QA Tester** | 1 | 25M | 150M |
| **Total** | **6** | **185M** | **1,350M** |

### 9.2 Infrastructure Cost (Monthly)

| Service | Cost/month |
|---------|------------|
| **AWS EC2** (t3.medium × 2) | 1.5M |
| **AWS S3** (100GB storage) | 500K |
| **AWS CloudFront** (CDN) | 1M |
| **Redis Cloud** | 500K |
| **OpenAI API** (1,000 students) | 1M |
| **Google TTS** (1,000 students) | 500K |
| **Domain & SSL** | 200K |
| **Monitoring** (Sentry, DataDog) | 500K |
| **Total** | **5.7M/month** |

### 9.3 Total Cost (6 tháng)

| Category | Cost |
|----------|------|
| **Development** | 1,350M |
| **Infrastructure** (6 tháng) | 34.2M |
| **Contingency** (10%) | 138.4M |
| **Total** | **1,522.6M** (~**$61K USD**) |

---

## 10. DEMO & TESTING

### 10.1 Demo Scenario

**Bài học mẫu:** "Present Simple Tense" (Tiếng Anh lớp 6)

#### **Step 1: Giáo viên upload**
- Upload file PDF (5 trang)
- Điền thông tin: Title, Subject, Grade, Duration
- Submit → Hệ thống xử lý 2-3 phút

#### **Step 2: AI xử lý**
- Extract text từ PDF
- GPT-4 phân tích → Tạo lesson plan
- Tạo 15 segments teaching script
- Tạo 10 câu hỏi
- Generate audio cho tất cả

#### **Step 3: Học sinh học**
- Chọn bài học
- AI giới thiệu (voice): "Chào em! Hôm nay..."
- Giảng bài (15 segments, ~20 phút)
- Đặt câu hỏi (10 câu)
- Học sinh trả lời
- AI feedback (voice)
- Kết thúc → Báo cáo kết quả

#### **Expected Result:**
- Thời gian: 30 phút
- Điểm số: 80/100
- Feedback: "Em làm tốt lắm! Cần ôn thêm phần thêm s/es"

---

### 10.2 Testing Checklist

#### **Functional Testing**
- [ ] Upload file (PDF, DOCX, PPT)
- [ ] Extract text correctly
- [ ] AI generates lesson plan
- [ ] AI generates teaching script
- [ ] AI generates questions
- [ ] TTS generates audio
- [ ] Audio plays correctly
- [ ] Questions display correctly
- [ ] Answer evaluation works
- [ ] Feedback is accurate
- [ ] Score calculation correct
- [ ] Progress tracking works
- [ ] Session can be resumed
- [ ] Reports are accurate

#### **Performance Testing**
- [ ] File upload < 5s
- [ ] AI processing < 3 min
- [ ] Audio generation < 10s per segment
- [ ] API response < 500ms
- [ ] Audio streaming smooth
- [ ] Handle 100 concurrent users

#### **Security Testing**
- [ ] Authentication works
- [ ] Authorization works
- [ ] File upload validation
- [ ] SQL injection prevention
- [ ] XSS prevention
- [ ] CSRF protection

---

## 📊 SUMMARY

### Key Highlights

✅ **Feasible:** Công nghệ đã sẵn có (GPT-4, TTS)  
✅ **Affordable:** Chi phí AI rất thấp ($0.036/student)  
✅ **Scalable:** Kiến trúc có thể scale lên 10,000+ users  
✅ **Fast:** MVP trong 2 tháng, Launch trong 6 tháng  
✅ **Valuable:** Giải quyết pain point lớn (thiếu giáo viên)  

### Next Steps

1. **Week 1:** Setup project, database, infrastructure
2. **Week 2:** Implement file upload & text extraction
3. **Week 3:** Integrate GPT-4 for content analysis
4. **Week 4:** Implement TTS & audio generation
5. **Week 5-6:** Build learning session flow
6. **Week 7-8:** Frontend & testing

---

## 🚀 LET'S BUILD IT!

Bạn muốn tôi:

1. **💻 Bắt đầu code ngay?** (Setup Laravel project, database migrations)
2. **🎨 Tạo mockup UI/UX?** (Figma design cho teacher & student interface)
3. **📝 Viết API documentation chi tiết?** (Swagger/OpenAPI spec)
4. **🧪 Tạo demo với GPT-4?** (Test AI prompts với bài học mẫu)
5. **📊 Tạo project plan chi tiết?** (Gantt chart, task breakdown)

Hãy cho tôi biết bạn muốn bắt đầu từ đâu! 🎯
