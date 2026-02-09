# Text-to-SQL: AI Tự Động Generate SQL Query

## 📚 Tài Liệu Hướng Dẫn

### **Cách 1: Text-to-SQL (Đã implement)**

File: `app/Services/AI/TextToSqlService.php`

#### **Cách hoạt động:**

1. **AI nhận câu hỏi** → Hiểu database schema
2. **Generate SQL** → Tạo query phù hợp
3. **Validate** → Kiểm tra an toàn (chỉ SELECT)
4. **Execute** → Chạy query và trả kết quả

#### **Sử dụng:**

```php
// Trong Controller hoặc Service
use App\Services\AI\TextToSqlService;

$textToSql = app(TextToSqlService::class);

// Hỏi trực tiếp
$result = $textToSql->ask("Có bao nhiêu học sinh đang học IELTS?");

// Kết quả:
// [
//     'success' => true,
//     'sql' => 'SELECT COUNT(*) as total FROM students...',
//     'explanation' => 'Query đếm số học sinh...',
//     'data' => [...],
//     'count' => 1
// ]
```

#### **Ví dụ câu hỏi:**

```
✅ "Có bao nhiêu học sinh đang học IELTS?"
→ SELECT COUNT(*) FROM students s 
   JOIN contracts c ON c.student_id = s.id 
   JOIN classes cl ON cl.id = c.class_id
   JOIN products p ON p.id = cl.product_id
   WHERE p.name LIKE '%IELTS%' AND s.status = 1

✅ "Lớp nào đang thiếu học sinh?"
→ SELECT c.cls_name, c.max_students, 
   COUNT(ct.id) as current_students
   FROM classes c
   LEFT JOIN contracts ct ON ct.class_id = c.id
   GROUP BY c.id
   HAVING current_students < c.max_students

✅ "Top 5 học sinh có công nợ cao nhất"
→ SELECT s.name, SUM(c.debt_amount) as total_debt
   FROM students s
   JOIN contracts c ON c.student_id = s.id
   GROUP BY s.id
   ORDER BY total_debt DESC
   LIMIT 5
```

---

### **Cách 2: RAG (Retrieval-Augmented Generation)**

Tìm kiếm semantic trong knowledge base.

#### **Cách hoạt động:**

1. **Embedding** → Chuyển text thành vector
2. **Vector Search** → Tìm documents tương tự
3. **Context Injection** → Đưa vào prompt
4. **Generate Answer** → AI trả lời dựa trên context

#### **Ưu điểm:**
- Tìm kiếm theo ngữ nghĩa (không cần từ khóa chính xác)
- Phù hợp cho tài liệu, hướng dẫn
- Không cần biết database schema

#### **Nhược điểm:**
- Cần vector database (Pinecone, Weaviate, Milvus...)
- Setup phức tạp hơn
- Chi phí cao hơn

---

### **Cách 3: Database Introspection**

AI tự động khám phá database schema.

#### **Implement:**

```php
class DatabaseIntrospectionService
{
    public function getFullSchema()
    {
        $tables = DB::select('SHOW TABLES');
        $schema = [];
        
        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];
            
            // Lấy columns
            $columns = DB::select("DESCRIBE {$tableName}");
            
            // Lấy relationships
            $foreignKeys = DB::select("
                SELECT 
                    COLUMN_NAME,
                    REFERENCED_TABLE_NAME,
                    REFERENCED_COLUMN_NAME
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE TABLE_NAME = '{$tableName}'
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            $schema[$tableName] = [
                'columns' => $columns,
                'foreign_keys' => $foreignKeys,
            ];
        }
        
        return $schema;
    }
}
```

---

## 🎯 **So Sánh Các Cách**

| Tiêu Chí | Text-to-SQL | RAG | Function Calling |
|----------|-------------|-----|------------------|
| **Setup** | Dễ ⭐⭐⭐ | Khó ⭐ | Trung bình ⭐⭐ |
| **Linh hoạt** | Cao ⭐⭐⭐ | Cao ⭐⭐⭐ | Thấp ⭐ |
| **An toàn** | Trung bình ⭐⭐ | Cao ⭐⭐⭐ | Cao ⭐⭐⭐ |
| **Chi phí** | Thấp ⭐⭐⭐ | Cao ⭐ | Thấp ⭐⭐⭐ |
| **Độ chính xác** | Cao ⭐⭐⭐ | Trung bình ⭐⭐ | Rất cao ⭐⭐⭐ |

---

## 💡 **Khuyến Nghị**

### **Dùng Text-to-SQL khi:**
✅ Cần query dữ liệu động  
✅ Không muốn khai báo nhiều functions  
✅ Database schema rõ ràng  
✅ Chấp nhận risk nhỏ về SQL injection  

### **Dùng Function Calling khi:**
✅ Cần control chặt chẽ  
✅ Business logic phức tạp  
✅ Bảo mật cao  
✅ Performance quan trọng  

### **Dùng RAG khi:**
✅ Tìm kiếm tài liệu, hướng dẫn  
✅ Semantic search  
✅ Không cần real-time data  

---

## 🚀 **Tích Hợp Text-to-SQL Vào Chatbot**

### **Bước 1: Thêm vào GeminiService**

```php
// app/Services/AI/GeminiService.php

public function chat($conversationId, $message)
{
    // ... existing code ...
    
    // Thêm Text-to-SQL capability
    $textToSql = app(TextToSqlService::class);
    
    // Detect nếu user hỏi về data
    if ($this->isDataQuery($message)) {
        $sqlResult = $textToSql->ask($message);
        
        if ($sqlResult['success']) {
            // Format kết quả đẹp
            $formattedData = $this->formatDataResult($sqlResult);
            
            // Lưu vào conversation
            $this->saveMessage($conversationId, 'assistant', $formattedData);
            
            return $formattedData;
        }
    }
    
    // Fallback to normal chat
    return $this->normalChat($conversationId, $message);
}

protected function isDataQuery($message)
{
    $dataKeywords = [
        'có bao nhiêu', 'tìm', 'danh sách', 'thống kê',
        'top', 'cao nhất', 'thấp nhất', 'tổng', 'trung bình'
    ];
    
    foreach ($dataKeywords as $keyword) {
        if (stripos($message, $keyword) !== false) {
            return true;
        }
    }
    
    return false;
}
```

### **Bước 2: Format kết quả đẹp**

```php
protected function formatDataResult($sqlResult)
{
    $output = "📊 **Kết quả tìm kiếm:**\n\n";
    
    // Hiển thị SQL (optional)
    $output .= "```sql\n{$sqlResult['sql']}\n```\n\n";
    
    // Hiển thị data dạng bảng
    if (!empty($sqlResult['data'])) {
        $output .= $this->arrayToMarkdownTable($sqlResult['data']);
    }
    
    $output .= "\n\n*Tìm thấy {$sqlResult['count']} kết quả*";
    
    return $output;
}

protected function arrayToMarkdownTable($data)
{
    if (empty($data)) return '';
    
    $first = (array)$data[0];
    $headers = array_keys($first);
    
    // Header
    $table = '| ' . implode(' | ', $headers) . ' |' . "\n";
    $table .= '|' . str_repeat(' --- |', count($headers)) . "\n";
    
    // Rows
    foreach ($data as $row) {
        $row = (array)$row;
        $table .= '| ' . implode(' | ', $row) . ' |' . "\n";
    }
    
    return $table;
}
```

---

## ⚠️ **Lưu Ý An Toàn**

### **1. SQL Injection Prevention:**
```php
// ✅ GOOD: Validate trước khi execute
protected function isSafeQuery($sql)
{
    // Chỉ cho SELECT
    // Không cho DROP, DELETE, UPDATE...
}

// ❌ BAD: Execute trực tiếp
DB::select($aiGeneratedSql); // NGUY HIỂM!
```

### **2. Rate Limiting:**
```php
// Giới hạn số query/user/ngày
if ($this->exceedsRateLimit($userId)) {
    return 'Bạn đã vượt quá giới hạn query hôm nay';
}
```

### **3. Query Timeout:**
```php
// Set timeout cho query
DB::statement('SET SESSION max_execution_time=5000'); // 5s
```

---

## 🎉 **Kết Luận**

**Text-to-SQL** là giải pháp tốt nhất cho use case của bạn vì:

✅ Không cần khai báo function cho mỗi câu hỏi  
✅ AI tự động hiểu và generate query  
✅ Linh hoạt, dễ mở rộng  
✅ Setup đơn giản  

**Đã implement:** `TextToSqlService.php`  
**Sẵn sàng sử dụng!** 🚀
