# Post Management API - Quick Start Guide

## ✅ Installation Complete

All components have been set up:

- ✅ MongoDB Laravel package (`mongodb/laravel-mongodb`)
- ✅ Post Model with MongoDB connection
- ✅ PostController with CRUD operations
- ✅ EmailVerificationController with code send/verify
- ✅ VerificationCodeMail mailable class
- ✅ Email verification template
- ✅ API routes configured
- ✅ CORS middleware enabled

## 🚀 Starting the Server

Since `php` isn't in your current shell PATH, use the full path:

```bash
"/c/Users/fuyad/AppData/Local/Microsoft/WinGet/Packages/PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe/php.exe" artisan serve --host 127.0.0.1 --port 8000
```

Or add PHP to PATH for this session:

```bash
export PATH="/c/Users/fuyad/AppData/Local/Microsoft/WinGet/Packages/PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe:$PATH"
php artisan serve --host 127.0.0.1 --port 8000
```

## 📡 API Endpoints

### Posts CRUD

**Create Post**
```bash
curl -X POST http://127.0.0.1:8000/api/posts \
  -H "Content-Type: application/json" \
  -d '{
    "title": "My First Post",
    "content": "Post content here",
    "author": "John Doe",
    "email": "john@example.com"
  }'
```

**Get All Posts**
```bash
curl http://127.0.0.1:8000/api/posts
```

**Get Single Post**
```bash
curl http://127.0.0.1:8000/api/posts/{post_id}
```

**Update Post**
```bash
curl -X PUT http://127.0.0.1:8000/api/posts/{post_id} \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Updated Title",
    "content": "Updated content"
  }'
```

**Delete Post**
```bash
curl -X DELETE http://127.0.0.1:8000/api/posts/{post_id}
```

### Email Verification

**Send Verification Code**
```bash
curl -X POST http://127.0.0.1:8000/api/send-verification-code \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com"
  }'
```

**Verify Email**
```bash
curl -X POST http://127.0.0.1:8000/api/verify-email \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "code": "123456"
  }'
```

## 🗂️ Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── PostController.php
│   │   └── EmailVerificationController.php
│   └── Middleware/
│       └── Cors.php
├── Mail/
│   └── VerificationCodeMail.php
└── Models/
    └── Post.php

routes/
└── api.php

resources/
└── views/
    └── emails/
        └── verification-code.blade.php

config/
└── database.php (MongoDB connection configured)

.env (MongoDB DSN configured)
```

## ⚙️ Configuration

### Database Connection (`.env`)
```env
DB_CONNECTION=mongodb
MONGO_DSN="mongodb+srv://fuyadswe:%40geif247@cluster0.ifcb6cw.mongodb.net/?retryWrites=true&w=majority"
MONGO_DATABASE=post_management
```

### Mail Configuration (Optional)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@example.com
```

## 📝 Response Formats

### Success Response
```json
{
  "success": true,
  "message": "Post created successfully",
  "data": {
    "_id": "507f1f77bcf86cd799439011",
    "title": "My First Post",
    "content": "Post content",
    "author": "John Doe",
    "email": "john@example.com",
    "email_verified": false,
    "created_at": "2025-11-17T10:00:00.000000Z",
    "updated_at": "2025-11-17T10:00:00.000000Z"
  }
}
```

### Error Response
```json
{
  "success": false,
  "message": "Validation error",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

## 🔍 Testing

Run the test command to check MongoDB connection:
```bash
php artisan test:mongo
```

View all routes:
```bash
php artisan route:list --path=api
```

## 🛠️ Troubleshooting

### MongoDB Connection Issues
If you encounter TLS errors with MongoDB Atlas:
1. Use local MongoDB instance
2. Try WSL2/Docker environment
3. Use MongoDB Data API (REST)

See `MONGODB_SETUP.md` for detailed solutions.

### CORS Issues
CORS middleware is already configured to allow all origins (`*`). For production, update `app/Http/Middleware/Cors.php` with specific domains.

### Email Not Sending
Verification codes are returned in API response for development. Uncomment the Mail line in `EmailVerificationController.php` once SMTP is configured.

## 🎯 Next Steps

1. Start the server with the full PHP path
2. Test API endpoints with curl or Postman
3. Configure mail settings if needed
4. Deploy to production environment

All code is ready to use!
