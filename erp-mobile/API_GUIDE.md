# API Configuration Guide for Mobile App

## Backend Requirements

Để ứng dụng mobile hoạt động tốt, backend Laravel cần đảm bảo các điểm sau:

## 1. CORS Configuration

Cập nhật file `config/cors.php`:

```php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['*'], // Trong production, chỉ định cụ thể domain
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
```

## 2. API Response Format

Tất cả API responses nên có format chuẩn:

```php
// Success response
return response()->json([
    'code' => 200,
    'message' => 'Success',
    'data' => $data
]);

// Error response
return response()->json([
    'code' => 400,
    'message' => 'Error message',
    'data' => null
], 400);
```

## 3. Authentication

### JWT Token
- Token được gửi trong header: `Authorization: Bearer {token}`
- Token expiry: Nên set thời gian hợp lý (ví dụ: 7 days)
- Refresh token: Nên implement để user không phải login lại thường xuyên

### Login Response Format
```php
return response()->json([
    'code' => 200,
    'message' => 'Login successful',
    'data' => [
        'token' => $token,
        'user' => [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'full_name' => $user->full_name,
            'avatar' => $user->avatar,
            'role_id' => $user->role_id,
            'branch_id' => $user->branch_id,
        ]
    ]
]);
```

## 4. Required API Endpoints

### Authentication APIs
- ✅ `POST /api/auth/login` - Already exists
- ✅ `POST /api/auth/logout` - Already exists
- ✅ `GET /api/menu` - Already exists

### Teacher APIs
- ✅ `GET /api/lms/teachers/show/{id}` - Already exists
- ✅ `POST /api/settings/classes/load-classes` - Already exists
- ✅ `POST /api/settings/classes/sessions` - Already exists
- ✅ `POST /api/lms/attendances/load-students` - Already exists
- ✅ `POST /api/lms/attendances/save` - Already exists
- ✅ `POST /api/lms/assessments/add` - Already exists
- ✅ `POST /api/lms/students/assessments` - Already exists

### Student APIs
- ✅ `GET /api/lms/students/show/{id}` - Already exists
- ✅ `POST /api/lms/students/contracts` - Already exists
- ✅ `POST /api/lms/students/sessions` - Already exists
- ✅ `POST /api/lms/students/assessments` - Already exists
- ✅ `POST /api/lms/students/payments` - Already exists
- ✅ `GET /api/crm/students/get_all_data/{parent_id}` - Already exists
- ✅ `GET /api/crm/ticket/get_all_data/{parent_id}` - Already exists

## 5. Additional Endpoints Needed (Optional)

### For better mobile experience, consider adding:

```php
// Get teacher's schedule for a date range
POST /api/mobile/teacher/schedule
{
    "teacher_id": 1,
    "start_date": "2026-01-26",
    "end_date": "2026-02-02"
}

// Get student's upcoming sessions
POST /api/mobile/student/upcoming-sessions
{
    "student_id": 1,
    "limit": 10
}

// Get notifications
GET /api/mobile/notifications
Response: {
    "code": 200,
    "data": [
        {
            "id": 1,
            "title": "Thông báo",
            "message": "Nội dung",
            "type": "info",
            "created_at": "2026-01-26 10:00:00",
            "is_read": false
        }
    ]
}

// Mark notification as read
POST /api/mobile/notifications/{id}/read
```

## 6. Image/File Handling

### Avatar Upload
Endpoint hiện tại:
- `POST /api/user/upload-avatar`
- `POST /api/lms/students/upload-avatar`

Response format:
```php
return response()->json([
    'code' => 200,
    'message' => 'Avatar uploaded successfully',
    'data' => [
        'avatar_url' => $avatarUrl
    ]
]);
```

### Image URLs
- Đảm bảo trả về full URL (bao gồm domain)
- Ví dụ: `https://yourdomain.com/storage/avatars/user1.jpg`

## 7. Pagination

Cho các list APIs, nên trả về pagination info:

```php
return response()->json([
    'code' => 200,
    'message' => 'Success',
    'data' => [
        'data' => $items,
        'total' => $total,
        'current_page' => $currentPage,
        'per_page' => $perPage,
        'last_page' => $lastPage
    ]
]);
```

## 8. Error Handling

### Validation Errors
```php
return response()->json([
    'code' => 422,
    'message' => 'Validation failed',
    'errors' => $validator->errors()
], 422);
```

### Unauthorized
```php
return response()->json([
    'code' => 401,
    'message' => 'Unauthorized'
], 401);
```

### Not Found
```php
return response()->json([
    'code' => 404,
    'message' => 'Resource not found'
], 404);
```

## 9. Testing APIs

### Using Postman/Insomnia

1. **Login:**
```
POST http://localhost:8000/api/auth/login
Body: {
    "username": "teacher1",
    "password": "password"
}
```

2. **Get Teacher Schedule:**
```
POST http://localhost:8000/api/settings/classes/sessions
Headers: {
    "Authorization": "Bearer {token}"
}
Body: {
    "teacher_id": 1,
    "start_date": "2026-01-26",
    "end_date": "2026-01-26"
}
```

## 10. Mobile-Specific Considerations

### Response Size
- Giới hạn số lượng records trả về
- Implement pagination
- Chỉ trả về fields cần thiết

### Performance
- Add database indexes cho các queries thường dùng
- Cache các data ít thay đổi
- Optimize N+1 queries

### Security
- Validate tất cả inputs
- Rate limiting cho APIs
- HTTPS only trong production
- Sanitize outputs

## 11. Deployment Checklist

- [ ] Update CORS allowed origins
- [ ] Set proper JWT expiry time
- [ ] Enable HTTPS
- [ ] Set up rate limiting
- [ ] Configure file upload limits
- [ ] Set up error logging
- [ ] Configure database connection pooling
- [ ] Set up API monitoring

## 12. Mobile App Configuration

Sau khi deploy backend, cập nhật trong mobile app:

File: `src/constants/config.ts`
```typescript
export const API_CONFIG = {
  BASE_URL: 'https://your-production-domain.com/api',
  TIMEOUT: 30000,
};
```

## Support

Nếu cần thêm endpoints hoặc có vấn đề với API, vui lòng liên hệ team backend.
