# User Management System

A comprehensive User Management System built with Laravel 10, featuring authentication, role-based access control (RBAC), and permissions management.

## Features

### 🔐 Authentication
- **Laravel Passport Integration**: Secure OAuth2 authentication
- **Login/Logout**: Token-based authentication with automatic token revocation
- **Role-Based Access Control**: Three-tier role system (SuperAdmin, Admin, User)

### 👥 User Management
- **User Listing**: Role-based user visibility
  - SuperAdmin: Can see all users (including Admins and Users)
  - Admin: Can see all users (but not SuperAdmins or Admins)
  - User: Can only see their own data
- **Bulk User Creation**: Asynchronous processing using Laravel Queues
- **User Updates**: Role-based update permissions
- **Soft Delete**: Users are soft deleted instead of permanent deletion

### 🚀 Performance & Caching
- **Redis Caching**: User listing is cached for 5 minutes
- **Queue System**: Background processing for bulk operations
- **Database Optimization**: Efficient queries with proper indexing

### 🛡️ Security
- **Role-Based Permissions**: Granular permission system
- **Input Validation**: Comprehensive request validation
- **Soft Deletes**: Data preservation with soft deletion
- **Token Management**: Secure token handling with Passport

## Technology Stack

- **Framework**: Laravel 10
- **Database**: MySQL
- **Authentication**: Laravel Passport (OAuth2)
- **Caching**: Redis
- **Queue**: Laravel Queue with Redis driver
- **API**: RESTful API with JSON responses

## Installation & Setup

### Prerequisites
- PHP 8.1 or higher
- Composer
- MySQL 5.7 or higher
- Redis (for caching and queues)

### 1. Clone and Install Dependencies
```bash
git clone <repository-url>
cd user-management-system
composer install
```

### 2. Environment Configuration
Copy the `.env.example` file and configure your environment:
```bash
cp .env.example .env
```

Update the following in your `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=user_management_system
DB_USERNAME=your_username
DB_PASSWORD=your_password

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 3. Generate Application Key
```bash
php artisan key:generate
```

### 4. Run Migrations and Seeders
```bash
php artisan migrate
php artisan db:seed
```

### 5. Install Passport
```bash
php artisan passport:install
```

### 6. Start Queue Worker (for background jobs)
```bash
php artisan queue:work
```

### 7. Start the Development Server
```bash
php artisan serve
```



**Role-based Responses:**
- **SuperAdmin**: All users (including soft deleted)
- **Admin**: Only regular users (no SuperAdmins or Admins)
- **User**: Only their own data

## Role-Based Access Control

### SuperAdmin
- **Full Control**: Can manage all users, including other SuperAdmins
- **Permissions**: Create, read, update, delete any user
- **Special Privileges**: Can delete users (soft delete)

### Admin
- **Limited Control**: Can manage regular users only
- **Permissions**: Create, read, update regular users
- **Restrictions**: Cannot manage SuperAdmins or other Admins
- **Cannot Delete**: Cannot delete any users

### User
- **Self-Management**: Can only view their own data
- **Permissions**: Read and update their own information
- **Restrictions**: Cannot see or manage other users

## Database Schema

### Users Table
- `id` - Primary key
- `name` - User's full name
- `email` - Unique email address
- `password` - Hashed password
- `role` - Enum: SuperAdmin, Admin, User
- `deleted_at` - Soft delete timestamp
- `created_at`, `updated_at` - Timestamps

### Permissions Table
- `id` - Primary key
- `name` - Permission name
- `slug` - Unique permission slug
- `description` - Permission description
- `created_at`, `updated_at` - Timestamps

### Role_Permissions Table
- `id` - Primary key
- `role` - Enum: SuperAdmin, Admin, User
- `permission_id` - Foreign key to permissions table
- `created_at`, `updated_at` - Timestamps

## Default Users

After running the seeders, the following users are created:

| Email | Password | Role |
|-------|----------|------|
| superadmin@example.com | password123 | SuperAdmin |
| admin@example.com | password123 | Admin |
| user@example.com | password123 | User |
| john@example.com | password123 | User |
| jane@example.com | password123 | User |

## Caching Strategy

- **User Listing**: Cached for 5 minutes per user role
- **Cache Key Format**: `users_list_{role}_{user_id}`
- **Cache Invalidation**: Automatically cleared on user operations

## Queue System

- **Bulk User Creation**: Processed asynchronously
- **Job Class**: `BulkCreateUsers`
- **Queue Driver**: Redis (configurable)
- **Error Handling**: Failed jobs are logged with detailed error information



## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
