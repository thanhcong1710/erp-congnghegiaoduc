# 🚀 HƯỚNG DẪN SETUP PROJECT AI_TUTOR

**Mục tiêu:** Tạo Laravel 11 project với kiến trúc multi-platform (Telegram, Discord, Web, Mobile)

---

## 📋 BƯỚC 1: TẠO PROJECT LARAVEL 11

### **Option 1: Dùng Composer (Khuyến nghị)**

```bash
# Di chuyển đến thư mục src
cd /Users/mac24h/Documents/docker-work/src

# Tạo project Laravel 11
composer create-project laravel/laravel ai_tutor

# Di chuyển vào project
cd ai_tutor
```

### **Option 2: Dùng Laravel Installer**

```bash
# Cài Laravel Installer (nếu chưa có)
composer global require laravel/installer

# Tạo project
laravel new ai_tutor
```

### **Option 3: Dùng Docker Composer**

```bash
# Tạo project bằng Docker
docker run --rm \
    -v $(pwd):/app \
    composer create-project laravel/laravel ai_tutor

# Hoặc nếu có docker-compose
docker-compose run --rm composer create-project laravel/laravel ai_tutor
```

---

## 📋 BƯỚC 2: CẤU TRÚC THƯ MỤC

Sau khi tạo xong, cấu trúc sẽ như sau:

```
ai_tutor/
├── app/
│   ├── Console/
│   ├── Exceptions/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/           # API cho tất cả platforms
│   │   │   │   ├── Telegram/  # Telegram-specific
│   │   │   │   ├── Discord/   # Discord-specific
│   │   │   │   ├── Web/       # Web app
│   │   │   │   └── Mobile/    # Mobile app
│   │   │   └── ...
│   │   ├── Middleware/
│   │   └── Requests/
│   ├── Models/
│   ├── Providers/
│   └── Services/              # Business logic (platform-agnostic)
│       ├── AI/
│       │   ├── LLMService.php
│       │   └── TTSService.php
│       ├── Lesson/
│       │   ├── LessonService.php
│       │   └── QuestionService.php
│       └── User/
│           └── UserService.php
├── bootstrap/
├── config/
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── public/
├── resources/
│   ├── views/
│   └── js/
├── routes/
│   ├── api.php              # API routes
│   ├── web.php              # Web routes
│   ├── telegram.php         # Telegram webhook routes
│   └── discord.php          # Discord webhook routes
├── storage/
├── tests/
├── .env
├── artisan
├── composer.json
└── package.json
```

---

## 📋 BƯỚC 3: CẤU HÌNH .ENV

```bash
# Copy .env.example
cp .env.example .env

# Generate app key
php artisan key:generate
```

**Cấu hình .env:**

```env
APP_NAME="AI Tutor"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ai_tutor
DB_USERNAME=root
DB_PASSWORD=

# Redis (for Queue)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Queue
QUEUE_CONNECTION=redis

# Telegram Bot
TELEGRAM_BOT_TOKEN=your_bot_token_here
TELEGRAM_WEBHOOK_URL=https://yourdomain.com/api/telegram/webhook

# Discord Bot
DISCORD_BOT_TOKEN=your_discord_token_here
DISCORD_WEBHOOK_URL=https://yourdomain.com/api/discord/webhook

# OpenAI
OPENAI_API_KEY=your_openai_key_here
OPENAI_MODEL=gpt-4o

# Google TTS
GOOGLE_APPLICATION_CREDENTIALS=/path/to/credentials.json

# AWS S3 (for audio storage)
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=ap-southeast-1
AWS_BUCKET=ai-tutor-audio
AWS_USE_PATH_STYLE_ENDPOINT=false
```

---

## 📋 BƯỚC 4: CÀI ĐẶT PACKAGES

```bash
# Core packages
composer require guzzlehttp/guzzle
composer require predis/predis

# Telegram Bot SDK
composer require telegram-bot-sdk/telegram-bot-sdk

# OpenAI PHP Client
composer require openai-php/laravel

# Google Cloud Text-to-Speech
composer require google/cloud-text-to-speech

# Laravel Sanctum (for API authentication)
composer require laravel/sanctum

# Development packages
composer require --dev laravel/pint
composer require --dev phpunit/phpunit
```

---

## 📋 BƯỚC 5: TẠO DATABASE

```bash
# Tạo database
mysql -u root -p -e "CREATE DATABASE ai_tutor CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Hoặc trong MySQL:
CREATE DATABASE ai_tutor CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

## 📋 BƯỚC 6: CHẠY MIGRATIONS

```bash
# Run migrations
php artisan migrate

# Seed data (optional)
php artisan db:seed
```

---

## 📋 BƯỚC 7: KIỂM TRA

```bash
# Start development server
php artisan serve

# Mở browser: http://localhost:8000
# Nếu thấy Laravel welcome page → Success! ✅
```

---

## 🎯 KIẾN TRÚC MULTI-PLATFORM

### **Nguyên tắc thiết kế:**

1. **Platform-agnostic Business Logic**
   - Tất cả logic nằm trong `Services/`
   - Controllers chỉ là thin layer

2. **Unified API**
   - Telegram, Discord, Web, Mobile đều dùng chung API
   - Chỉ khác nhau ở input/output format

3. **Adapter Pattern**
   - Mỗi platform có adapter riêng
   - Convert platform-specific format → unified format

### **Ví dụ:**

```php
// app/Services/Lesson/LessonService.php
class LessonService
{
    public function createLesson($userId, $subject, $level)
    {
        // Business logic (platform-agnostic)
        // Telegram, Discord, Web, Mobile đều dùng hàm này
    }
}

// app/Http/Controllers/Api/Telegram/LessonController.php
class LessonController extends Controller
{
    public function __construct(
        private LessonService $lessonService
    ) {}
    
    public function create(Request $request)
    {
        // Telegram-specific input handling
        $telegramUserId = $request->input('telegram_user_id');
        
        // Call platform-agnostic service
        $lesson = $this->lessonService->createLesson(
            $telegramUserId,
            $request->input('subject'),
            $request->input('level')
        );
        
        // Telegram-specific output formatting
        return response()->json([
            'message' => 'Lesson created',
            'lesson' => $lesson,
        ]);
    }
}

// app/Http/Controllers/Api/Discord/LessonController.php
class LessonController extends Controller
{
    public function __construct(
        private LessonService $lessonService
    ) {}
    
    public function create(Request $request)
    {
        // Discord-specific input handling
        $discordUserId = $request->input('discord_user_id');
        
        // Call SAME platform-agnostic service
        $lesson = $this->lessonService->createLesson(
            $discordUserId,
            $request->input('subject'),
            $request->input('level')
        );
        
        // Discord-specific output formatting
        return response()->json([
            'embed' => [
                'title' => 'Lesson Created',
                'description' => $lesson->title,
            ],
        ]);
    }
}
```

---

## 🚀 NEXT STEPS

Sau khi setup xong, chúng ta sẽ:

1. ✅ Tạo database migrations
2. ✅ Tạo Models
3. ✅ Tạo Services (Business Logic)
4. ✅ Tạo API Controllers
5. ✅ Tích hợp Telegram Bot
6. ✅ Tích hợp OpenAI GPT-4
7. ✅ Tích hợp Google TTS

---

## 📞 CẦN GIÚP?

Nếu gặp lỗi, hãy cho tôi biết:
- Error message
- Bước nào bị lỗi
- PHP version, Composer version

Tôi sẽ giúp bạn fix ngay! 🎯
