# Post Scheduler

A Laravel + Vue.js based application for scheduling and managing social media posts across multiple platforms like Twitter, Instagram, LinkedIn, and more.

---

## 📌 Core Features

### ✅ Authentication & User Management
- **User Registration/Login**: Secure authentication using Laravel Sanctum for API token management
- **Profile Management**: Basic user profile operations (view/edit)

### 📝 Post Management System
- **CRUD Operations**: Full create, read, update, delete functionality for posts
- **Multi-Platform Publishing**: Select multiple platforms for each post
- **Post Filtering**: Filter by status (Draft/Scheduled/Published) and date ranges
- **Post Status Tracking**: 
  - Draft: Post not ready for scheduling
  - Scheduled: Queued for future publishing
  - Published: Successfully sent to platforms

### 📱 Platform Integration
- **Platform Configuration**: View and manage available social platforms
- **User-Specific Access**: Toggle platform availability per user via pivot table
- **Content Validation**: Enforces platform-specific rules (e.g., Twitter's 280-character limit)

### ⏱️ Automated Scheduling System
- **Queue-Based Publishing**: Uses Laravel Jobs (`PublishPostJob`) for reliable scheduling
- **Status Tracking**: Records success/failure for each platform attempt
- **Error Handling**: 
  - Implements logging for all publishing attempts
  - Includes automatic retry logic for failed attempts
- **Platform-Specific Logic**: Custom handlers for each social platform's API requirements

### 📡 Real-time Activity Monitoring
- **Event Broadcasting**: Uses Laravel Events (`UserActionOccurred`) for real-time updates
- **Activity Logging**: 
  - Stores all user actions in dedicated `activity_logs` table
  - Tracks: user_id, action type, and descriptive context

---

## 🖼️ Frontend Components

### ✍️ Post Editor Interface
- **Form Fields**: Title, content body, image upload capability
- **Platform Selector**: Checkbox-based platform selection
- **Scheduling Controls**: DateTime picker for post scheduling
- **Content Assist**: Character counters with platform-specific limits

### 📊 Dashboard Views
- **Calendar View**: Visual timeline of scheduled posts
- **List View**: Tabular display with filtering options
- **Status Indicators**: Color-coded badges for post states

### ⚙️ Configuration Panel
- **Platform Management**: Toggle switches to enable/disable platforms per user

---

## 🧱 Database Architecture

### Core Models
- **User**: Base user account (id, name, email, password_hash)
- **Post**: Content to be published (id, title, content, image_url, scheduled_at, status, user_id)
- **Platform**: Social media platforms (id, name, platform_type)

### Relationship Tables
- **PostPlatform**: Junction table tracking post-platform status (post_id, platform_id, publish_status)
- **PlatformUser**: User-platform permissions (user_id, platform_id, enabled_flag)
- **ActivityLog**: Audit trail of user actions (id, user_id, action_type, description)

---

## 🚀 API Structure

### Authentication Endpoints
- `POST /api/register` - Create new user account
- `POST /api/login` - Generate authentication token
- `GET /api/profile` - Retrieve authenticated user data
- `Post /api/api/profile` - Update authenticated user data

### Post Management Endpoints
- `GET /api/posts` - List all user's posts (with filters)
- `POST /api/posts` - Create new  post
- `PUT /api/posts/{id}` - Update existing post
- `DELETE /api/posts/{id}` - Remove  post

### Platform Configuration Endpoints
- `GET /api/platforms` - List available platforms
- `POST /api/platforms` - Create new  platform
- `PUT /api/platforms/{id}` - Update existing platform
- `DELETE /api/platforms/{id}` - Remove  platforms
- `POST /api/platforms/toggle` - Enable/disable platform for user


### Analytics Configuration Endpoints
- `GET /api/analytics/posts` - List analytics 

---

## 🛠️ Technology Stack

### Backend
- **Framework**: Laravel 10
- **Authentication**: Laravel Sanctum (API tokens)
- **Asynchronous Processing**: Laravel Queues + Job retry logic
- **Real-time Updates**: Laravel Events + Broadcasting

### Frontend
- **Framework**: Vue.js 3 (Composition API)
- **UI Toolkit**: Bootstrap 5
- **Real-time Comms**: Laravel Echo + WebSockets

### Infrastructure
- **Database**: MySQL
- **Queue Driver**: Redis/database
- **Broadcasting**: Pusher or native WebSockets

---

## 📦 Deployment Instructions

```bash
# Clone repository
git clone https://github.com/raedhoussin/post-scheduler.git
cd post-scheduler

# Install backend dependencies
composer install

# Install frontend dependencies
npm install && npm run dev

# Configure environment
cp .env.example .env
php artisan key:generate

# Initialize database
php artisan migrate --seed

# Launch development server
#http://localhost:8000
php artisan serve

# Initialize API Swagger DOC 
#http://localhost:8000/api/documentation
php artisan l5-swagger:generate

#To Launch Web Socket :
# Real Time Update For Activity Logs :
#"channel('user-activities')"
#event :UserActionOccurred
php artisan websockets:serve

#email : raedhoussin33@gmail.com
#password : password123
