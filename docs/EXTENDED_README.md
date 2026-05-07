# Noor Alhuda LMS - Extended Documentation

<p align="center">
  <a href="https://github.com/RamiAlAjo/noor-alhuda-lms/actions"><img src="https://img.shields.io/github/actions/workflow/status/RamiAlAjo/noor-alhuda-lms?label=Build" alt="Build Status"></a>
  <a href="https://github.com/RamiAlAjo/noor-alhuda-lms/releases"><img src="https://img.shields.io/github/v/release/RamiAlAjo/noor-alhuda-lms?include_prereleases&label=Version" alt="Version"></a>
  <a href="https://github.com/RamiAlAjo/noor-alhuda-lms/stargazers"><img src="https://img.shields.io/github/stars/RamiAlAjo/noor-alhuda-lms?label=Stars" alt="Stars"></a>
  <a href="https://github.com/RamiAlAjo/noor-alhuda-lms/blob/main/LICENSE"><img src="https://img.shields.io/github/license/RamiAlAjo/noor-alhuda-lms?label=License" alt="License"></a>
  <a href="https://php.net/"><img src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php&logoColor=white" alt="PHP"></a>
  <a href="https://laravel.com/"><img src="https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white" alt="Laravel"></a>
</p>

---

## Table of Contents

- [Noor Alhuda LMS - Extended Documentation](#noor-alhuda-lms---extended-documentation)
    - [Table of Contents](#table-of-contents)
    - [Introduction](#introduction)
    - [Features](#features)
        - [User Management](#user-management)
        - [Course Management](#course-management)
        - [Enrollment System](#enrollment-system)
        - [Assessment \& Grading](#assessment--grading)
        - [Attendance Tracking](#attendance-tracking)
        - [Content Management](#content-management)
        - [Communication](#communication)
        - [Financial Management](#financial-management)
        - [Reporting \& Analytics](#reporting--analytics)
        - [AI Capacity Management](#ai-capacity-management)
        - [Search Functionality](#search-functionality)
        - [Notification System](#notification-system)
        - [Multi-Language Support](#multi-language-support)
        - [Theme \& Accessibility](#theme--accessibility)
    - [Prerequisites](#prerequisites)
        - [Server Requirements](#server-requirements)
        - [Web Server Requirements](#web-server-requirements)
        - [Additional Requirements](#additional-requirements)
        - [Operating System](#operating-system)
    - [Installation](#installation)
        - [Step 1: Clone Repository](#step-1-clone-repository)
        - [Step 2: Install Dependencies](#step-2-install-dependencies)
        - [Step 3: Environment Configuration](#step-3-environment-configuration)
        - [Step 4: Database Setup](#step-4-database-setup)
            - [Option A: SQLite (Development)](#option-a-sqlite-development)
            - [Option B: MySQL](#option-b-mysql)
        - [Step 5: Payment Gateway Configuration](#step-5-payment-gateway-configuration)
            - [Stripe Configuration](#stripe-configuration)
            - [PayPal Configuration](#paypal-configuration)
        - [Step 6: Build Assets](#step-6-build-assets)
        - [Step 7: Run Application](#step-7-run-application)
        - [Default Login Credentials](#default-login-credentials)
    - [Configuration](#configuration)
        - [Application Settings](#application-settings)
        - [Database Configuration](#database-configuration)
        - [Mail Configuration](#mail-configuration)
        - [Storage Configuration](#storage-configuration)
        - [Cache Configuration](#cache-configuration)
        - [Queue Configuration](#queue-configuration)
    - [Usage Examples](#usage-examples)
        - [Admin Usage](#admin-usage)
            - [Creating a New Course](#creating-a-new-course)
            - [Managing Users](#managing-users)
            - [Managing Academic Years](#managing-academic-years)
        - [Teacher Usage](#teacher-usage)
            - [Creating a Quiz](#creating-a-quiz)
            - [Taking Attendance](#taking-attendance)
            - [Entering Grades](#entering-grades)
        - [Student Usage](#student-usage)
            - [Enrolling in a Course](#enrolling-in-a-course)
            - [Taking a Quiz](#taking-a-quiz)
            - [Viewing Grades](#viewing-grades)
    - [Command-Line Interface](#command-line-interface)
        - [Artisan Commands](#artisan-commands)
            - [Application Commands](#application-commands)
            - [Database Commands](#database-commands)
            - [User Management Commands](#user-management-commands)
            - [Queue Commands](#queue-commands)
            - [Scheduler Commands](#scheduler-commands)
        - [Custom Commands](#custom-commands)
    - [Project Structure](#project-structure)
        - [Directory Overview](#directory-overview)
        - [Model Structure](#model-structure)
        - [Controller Structure](#controller-structure)
    - [Contribution Guidelines](#contribution-guidelines)
        - [How to Contribute](#how-to-contribute)
        - [Pull Request Process](#pull-request-process)
        - [Coding Standards](#coding-standards)
    - [Code of Conduct](#code-of-conduct)
        - [Our Pledge](#our-pledge)
        - [Our Standards](#our-standards)
        - [Enforcement](#enforcement)
    - [Licensing](#licensing)
        - [What You Can Do](#what-you-can-do)
        - [What You Must Do](#what-you-must-do)
        - [What You Cannot Do](#what-you-cannot-do)
    - [Versioning Policy](#versioning-policy)
        - [Version Format](#version-format)
        - [Release Schedule](#release-schedule)
    - [Frequently Asked Questions](#frequently-asked-questions)
        - [General Questions](#general-questions)
        - [Technical Questions](#technical-questions)
        - [Installation Questions](#installation-questions)
        - [Feature Questions](#feature-questions)
    - [Support](#support)
        - [Getting Help](#getting-help)
        - [Reporting Bugs](#reporting-bugs)
        - [Feature Requests](#feature-requests)

---

## Introduction

Welcome to **Noor Alhuda LMS** (Learning Management System), a comprehensive web-based platform designed for educational institutions to manage courses, enrollments, assessments, grades, attendance, and financial transactions. Built with **Laravel 12** and **Flux UI**, this system provides a modern, responsive interface for administrators, teachers, and students.

Noor Alhuda LMS aims to streamline academic processes, enhance communication between stakeholders, and provide a seamless learning experience. Whether you're running a small training center or a large educational institution, this system scales to meet your needs.

---

## Features

### User Management

The system provides robust user management with role-based access control:

- **Multi-Role System**: Support for Admin, Teacher, and Student roles with granular permissions
- **User Profiles**: Comprehensive user profiles with personal information, contact details, and profile pictures
- **User Settings**: customizable preferences including language, theme, and notification settings
- **User Impersonation**: Administrators can impersonate other users for testing and support purposes
- **Activity Logging**: All user activities are logged for audit purposes
- **Password Management**: Secure password reset and change functionality via Laravel Fortify
- **Bulk Operations**: Create, import, and manage multiple users simultaneously

### Course Management

Comprehensive course management capabilities:

- **Course Creation**: Create courses with detailed information including code, name, description, credits, and syllabus
- **Course Offerings**: Define course offerings per semester/academic year
- **Section Management**: Create multiple sections for each course with different schedules, rooms, and capacities
- **Schedule Management**: Define weekly schedules with day, start time, end time, and room assignments
- **Capacity Tracking**: Monitor enrollment numbers against maximum capacity
- **Course Visibility**: Control course visibility (draft, published, archived)
- **Course Prerequisites**: Define prerequisite courses that must be completed before enrollment
- **Department Association**: Associate courses with specific departments
- **Instructor Assignment**: Assign teachers to course sections

### Enrollment System

Flexible enrollment management:

- **Student Enrollment**: Students can request enrollment in available courses
- **Enrollment Approval Workflow**: Configurable approval process (auto-approve or manual approval)
- **Enrollment Requests**: Teachers can review and approve/reject enrollment requests
- **Bulk Enrollment**: Administrators can enroll multiple students at once
- **Enrollment Status Tracking**: Track enrollment status (pending, approved, rejected, dropped)
- **Waitlist Management**: Automatic waitlist when course reaches capacity
- **Enrollment Reports**: View enrollment history and statistics
- **Course Drop**: Students can drop courses within specified deadlines

### Assessment & Grading

Complete assessment and grading system:

- **Assessment Types**: Create various assessment types (Quiz, Exam, Assignment, Project, Participation)
- **Grade Entry**: Teachers can enter grades for student assessments
- **Grade Calculations**: Automatic calculation of weighted grades
- **Grade Locks**: Lock grades to prevent further modifications after posting
- **Grade History**: Track all grade changes with timestamps
- **Grade Appeals**: Students can appeal grades with a structured review process
- **Transcripts**: Generate official transcripts for students
- **Quiz System**: Comprehensive quiz functionality with multiple question types
- **Quiz Attempts**: Track quiz attempts and scores
- **Quiz Accommodations**: Provide extended time and other accommodations for students with needs
- **Grade Distribution Reports**: Visual analytics of grade distributions

### Attendance Tracking

Session-based attendance management:

- **Session Management**: Create attendance sessions for each course section
- **Mark Attendance**: Teachers can mark attendance for each session
- **Attendance Statuses**: Present, Absent, Late, Excused
- **Excused Absences**: Students can request excused absences with supporting documentation
- **Medical Leaves**: Extended absence management with medical documentation
- **Attendance Reports**: Generate attendance reports by student or course
- **Attendance Analytics**: Track attendance patterns and trends

### Content Management

Course content delivery system:

- **Course Materials**: Upload and organize course materials (PDFs, documents, videos)
- **Weekly Content**: Structure content by week/module
- **File Management**: Organize files in folders
- **Content Visibility**: Control when content is visible to students
- **Resource Links**: Add external resource links
- **Content Preview**: Preview content before publishing

### Communication

Integrated communication tools:

- **Announcements**: Create course-wide announcements with rich text
- **Discussion Forums**: Threaded discussions per course
- **Discussion Topics**: Organize discussions by topics
- **Discussion Replies**: Students and teachers can reply to discussions
- **Notifications**: In-app notifications for important events
- **Notification Preferences**: Users can customize notification preferences
- **Messaging**: Internal messaging system between users
- **Email Notifications**: Optional email notifications for important events
- **Calendar Events**: Integrated calendar for events and deadlines

### Financial Management

Complete fee and payment management:

- **Fee Types**: Create various fee types (tuition, registration, books, exams)
- **Fee Assignment**: Assign fees to students individually or in bulk
- **Payment Recording**: Record payments via multiple methods
- **Payment Gateway Integration**: Support for Stripe and PayPal
- **Invoice Generation**: Generate and send invoices
- **Payment History**: Complete payment history per student
- **Fee Waivers**: Apply waivers or discounts
- **Financial Reports**: Revenue reports and financial analytics

### Reporting & Analytics

Comprehensive reporting system:

- **Dashboard Analytics**: Overview statistics and charts
- **Enrollment Trends**: Track enrollment over time
- **Course Distribution**: Analyze course popularity
- **Student Progress**: Track student progress across courses
- **Grade Analytics**: Grade distribution and statistics
- **Attendance Reports**: Attendance summary and trends
- **Financial Reports**: Revenue and payment reports
- **Custom Reports**: Generate custom reports with filters

### AI Capacity Management

Intelligent capacity optimization using machine learning:

- **Capacity Prediction**: AI-powered enrollment forecasting using historical data
- **Feature Engineering**: Advanced data preprocessing and feature extraction
- **Prediction Models**: Multiple algorithms with confidence scoring and accuracy tracking
- **Optimization Recommendations**: Automated suggestions for capacity adjustments
- **Batch Processing**: Mass prediction runs for entire semesters
- **Real-time Analytics**: Live capacity monitoring and utilization tracking
- **Fallback Logic**: Rule-based predictions when ML service unavailable
- **Performance Metrics**: Model accuracy tracking and prediction confidence scores

### Search Functionality

Global search across all system entities:

- **Global Search API**: RESTful search endpoint with real-time results
- **Multi-entity Search**: Search users, courses, course offerings, and announcements
- **Role-based Results**: Context-aware search results filtered by user permissions
- **Keyboard Navigation**: Full keyboard support with arrow keys and Enter selection
- **Debounced Search**: Optimized search with 300ms debouncing for performance
- **Visual Highlighting**: Search terms highlighted in results
- **Smart Ranking**: Results ordered by relevance and recency

### Notification System

Comprehensive notification management:

- **Real-time Notifications**: WebSocket-based live notifications
- **Notification Types**: Support for grades, enrollments, payments, announcements, reminders
- **Broadcasting System**: Event-driven notifications with Laravel Broadcasting
- **Sound Notifications**: Web Audio API implementation with user preferences
- **Toast Messages**: Non-intrusive popup notifications with auto-dismiss
- **Notification Management**: Mark as read, delete, bulk operations
- **Type-specific Icons**: Color-coded notifications with appropriate icons
- **Mobile Responsive**: Optimized for all device sizes

### Multi-Language Support

Full internationalization:

- **Supported Languages**:
    - English (en)
    - Arabic (ar) - RTL support
    - Farsi (fa)
    - Turkish (tr)
    - French (fr)
    - Chinese (zh)
    - Indonesian (id)
    - Kurdish (ku)
    - Armenian (hy)
- **Language Switcher**: Easy language switching in the UI
- **RTL Support**: Full right-to-left layout support for Arabic
- **Localized Content**: All system messages localized
- **Date/Time Localization**: Date and time in user's locale

### Theme & Accessibility

Comprehensive theming and accessibility system:

- **Light/Dark Mode**: Toggle between light and dark themes with system preference detection
- **Theme Persistence**: User theme preferences saved across sessions
- **Custom Accent Colors**: 18+ accent colors with visual selection indicators
- **Accent Color Persistence**: Selected accent colors maintained across sessions
- **Responsive Design**: Optimized for desktop, tablet, and mobile devices
- **Accessibility Features**: WCAG compliant with screen reader support and keyboard navigation
- **High Contrast Mode**: Enhanced contrast for users with visual impairments
- **Reduced Motion**: Respects user's motion preferences
- **Focus Indicators**: Clear focus rings for keyboard navigation
- **Student Accommodations**: Comprehensive accommodation request management
- **Accommodation Types**: Extended time, alternative testing, large text, dyslexia fonts, etc.
- **Medical Records**: Integrated medical record management for accommodation requests

---

## Prerequisites

Before installing Noor Alhuda LMS, ensure your system meets the following requirements:

### Server Requirements

| Requirement | Minimum | Recommended |
| ----------- | ------- | ----------- |
| PHP         | 8.2+    | 8.2+        |
| Composer    | 2.0+    | Latest      |
| Node.js     | 18+     | 20+         |
| NPM         | 9+      | Latest      |
| MySQL       | 8.0+    | 8.0+        |
| OR SQLite   | 3.35+   | Latest      |

### Web Server Requirements

- Apache with mod_rewrite enabled
- OR Nginx with PHP-FPM
- OR Laravel Valet (macOS)
- OR Laravel Herd (Windows/macOS)

### Additional Requirements

- OpenSSL PHP Extension
- PDO PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- Ctype PHP Extension
- JSON PHP Extension
- BCMath PHP Extension
- Fileinfo PHP Extension
- Zip PHP Extension

### Operating System

- Linux (Ubuntu, Debian, CentOS, etc.)
- macOS
- Windows 10/11 with WSL2

---

## Installation

### Step 1: Clone Repository

```bash
git clone https://github.com/RamiAlAjo/noor-alhuda-lms.git
cd noor-alhuda-lms
```

### Step 2: Install Dependencies

Install PHP dependencies:

```bash
composer install
```

Install Node.js dependencies:

```bash
npm install
```

### Step 3: Environment Configuration

Copy the example environment file:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

### Step 4: Database Setup

#### Option A: SQLite (Development)

```bash
touch database/database.sqlite
```

Update `.env`:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite
```

#### Option B: MySQL

Update `.env` with your MySQL credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=noor_lms
DB_USERNAME=root
DB_PASSWORD=your_password
```

Create the database:

```bash
mysql -u root -p -e "CREATE DATABASE noor_lms;"
```

Run migrations:

```bash
php artisan migrate
```

Seed the database with sample data:

```bash
php artisan db:seed
```

### Step 5: Payment Gateway Configuration

#### Stripe Configuration

1. Create a Stripe account at https://dashboard.stripe.com
2. Get your API keys from the Stripe dashboard
3. Add the following to your `.env`:

```env
STRIPE_ENABLED=true
STRIPE_KEY=pk_test_your_key
STRIPE_SECRET=sk_test_your_key
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret
```

For production, use live keys:

```env
STRIPE_KEY=pk_live_your_key
STRIPE_SECRET=sk_live_your_key
```

#### PayPal Configuration

1. Create a PayPal developer account at https://developer.paypal.com
2. Get your client ID and secret
3. Add the following to your `.env`:

```env
PAYPAL_ENABLED=true
PAYPAL_MODE=sandbox
PAYPAL_CLIENT_ID=your_client_id
PAYPAL_CLIENT_SECRET=your_client_secret
```

For production, change mode to live:

```env
PAYPAL_MODE=live
```

### Step 6: Build Assets

```bash
npm run build
```

### Step 7: Run Application

Start the development server:

```bash
php artisan serve
```

Access the application at http://localhost:8000

### Default Login Credentials

| Role    | Email                    | Password |
| ------- | ------------------------ | -------- |
| Admin   | admin@noorlms.com        | password |
| Teacher | john.smith@noorlms.com   | password |
| Student | ahmed.hassan@noorlms.com | password |

> [!IMPORTANT]
> Change all default passwords immediately after first login!

---

## Configuration

### Application Settings

Configure your application in `.env`:

```env
APP_NAME="Noor Alhuda LMS"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost
APP_TIMEZONE=UTC
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US
```

### Database Configuration

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=noor_lms
DB_USERNAME=root
DB_PASSWORD=
```

### Mail Configuration

```env
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@noorlms.com"
MAIL_FROM_NAME="${APP_NAME}"
```

For production mail:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
```

### Storage Configuration

```env
FILESYSTEM_DISK=local
```

For S3:

```env
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your_key
AWS_SECRET_ACCESS_KEY=your_secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your_bucket
```

### Cache Configuration

```env
CACHE_DRIVER=file
```

For Redis:

```env
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### Queue Configuration

```env
QUEUE_CONNECTION=sync
```

For production:

```env
QUEUE_CONNECTION=redis
```

---

## Usage Examples

### Admin Usage

#### Creating a New Course

1. Log in as Admin
2. Navigate to **Academic > Courses**
3. Click **Add New Course**
4. Fill in course details:
    - Course Code: e.g., CS101
    - Course Name: e.g., Introduction to Programming
    - Credits: 3
    - Department: Computer Science
    - Description: Course description
5. Click **Save Course**

#### Managing Users

1. Navigate to **Users > All Users**
2. Click **Add User**
3. Select role (Admin, Teacher, Student)
4. Fill in user details
5. Set initial password
6. Click **Create User**

#### Managing Academic Years

1. Navigate to **Academic > Academic Years**
2. Click **Add Academic Year**
3. Enter year (e.g., 2024-2025)
4. Set start and end dates
5. Mark as current if applicable

### Teacher Usage

#### Creating a Quiz

1. Log in as Teacher
2. Navigate to your course
3. Go to **Quizzes > Create Quiz**
4. Fill in quiz details:
    - Title: Quiz 1
    - Description: Chapter 1 Quiz
    - Time Limit: 30 minutes
    - Max Attempts: 2
    - Passing Score: 60%
5. Add questions
6. Set correct answers
7. Click **Publish**

#### Taking Attendance

1. Navigate to your course
2. Go to **Attendance**
3. Select session date
4. Mark each student:
    - Present (✓)
    - Absent (X)
    - Late (L)
    - Excused (E)
5. Click **Save Attendance**

#### Entering Grades

1. Navigate to your course
2. Go to **Grades**
3. Select assessment type
4. Enter grades for each student
5. Click **Save Grades**
6. Optionally lock grades to prevent changes

### Student Usage

#### Enrolling in a Course

1. Log in as Student
2. Navigate to **Browse Courses**
3. Find desired course
4. Click **Enroll**
5. Wait for approval (if required)
6. Access course from **My Courses**

#### Taking a Quiz

1. Navigate to enrolled course
2. Go to **Quizzes**
3. Click **Start Quiz**
4. Answer questions within time limit
5. Click **Submit**
6. View results and score

#### Viewing Grades

1. Navigate to **My Grades**
2. View all course grades
3. Click on individual assessment for details
4. Download transcript if needed

---

## Command-Line Interface

### Artisan Commands

#### Application Commands

```bash
# Clear all caches
php artisan optimize:clear

# Cache configuration
php artisan config:cache
php artisan config:clear

# Cache routes
php artisan route:cache
php artisan route:clear

# Cache views
php artisan view:cache
php artisan view:clear

# Clear compiled class
php artisan clear-compiled
```

#### Database Commands

```bash
# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Reset migrations
php artisan migrate:reset

# Fresh migrate (drop all and re-run)
php artisan migrate:fresh

# Seed database
php artisan db:seed

# Show migration status
php artisan migrate:status
```

#### User Management Commands

```bash
# Create admin user
php artisan make:admin

# List all users
php artisan user:list

# Reset user password
php artisan user:reset-password email@example.com
```

#### Queue Commands

```bash
# Start queue worker
php artisan queue:work

# Process first queue job
php artisan queue:work --once

# Listen to queue
php artisan queue:listen

# Retry failed jobs
php artisan queue:retry

# List failed jobs
php artisan queue:failed
```

#### Scheduler Commands

```bash
# Run scheduler
php artisan schedule:run
```

### Custom Commands

```bash
# Generate sample data
php artisan db:seed

# Send scheduled notifications
php artisan notify:scheduled

# Generate reports
php artisan report:generate --type=enrollment

# Export data
php artisan export:students --format=csv
```

---

## Project Structure

### Directory Overview

```
noor-alhuda-lms/
├── app/
│   ├── Actions/                    # Laravel Actions (SOLID)
│   │   └── Fortify/               # Fortify user actions
│   ├── Concerns/                  # Shared traits
│   │   ├── PasswordValidationRules.php
│   │   └── ProfileValidationRules.php
│   ├── Events/                    # Application events
│   │   └── NotificationSent.php
│   ├── Helpers/                   # Helper functions
│   │   └── helpers.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/            # Admin controllers
│   │   │   │   ├── AcademicController.php
│   │   │   │   ├── AcademicStandingController.php
│   │   │   │   ├── AccommodationController.php
│   │   │   │   ├── ActivityLogController.php
│   │   │   │   ├── AnnouncementController.php
│   │   │   │   ├── CourseController.php
│   │   │   │   ├── CourseFeedbackController.php
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── EnrollmentController.php
│   │   │   │   ├── FeeController.php
│   │   │   │   ├── GradeAppealController.php
│   │   │   │   ├── MedicalController.php
│   │   │   │   ├── MedicalLeaveController.php
│   │   │   │   ├── ReportController.php
│   │   │   │   ├── SettingController.php
│   │   │   │   └── UserController.php
│   │   │   ├── Api/              # API controllers
│   │   │   ├── Student/          # Student controllers
│   │   │   │   ├── CourseController.php
│   │   │   │   ├── CourseFeedbackController.php
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── DiscussionController.php
│   │   │   │   ├── ExcusedAbsenceController.php
│   │   │   │   ├── GradeAppealController.php
│   │   │   │   ├── GradeController.php
│   │   │   │   ├── MedicalController.php
│   │   │   │   ├── MedicalLeaveController.php
│   │   │   │   ├── PaymentController.php
│   │   │   │   ├── QuizController.php
│   │   │   │   └── TranscriptController.php
│   │   │   ├── Teacher/          # Teacher controllers
│   │   │   │   ├── AccommodationController.php
│   │   │   │   ├── AnnouncementController.php
│   │   │   │   ├── CourseController.php
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── DiscussionController.php
│   │   │   │   ├── ExcusedAbsenceController.php
│   │   │   │   ├── GradeAppealController.php
│   │   │   │   ├── QuizController.php
│   │   │   │   └── ReportController.php
│   │   │   ├── Controller.php
│   │   │   ├── CalendarController.php
│   │   │   ├── MessageController.php
│   │   │   ├── NoteController.php
│   │   │   ├── ReminderController.php
│   │   │   ├── TaskController.php
│   │   │   ├── ThemeController.php
│   │   │   └── UserSettingsController.php
│   │   ├── Middleware/           # Custom middleware
│   │   │   ├── ApplyLocale.php
│   │   │   └── RoleMiddleware.php
│   │   └── Requests/             # Form requests
│   ├── Livewire/                 # Livewire components
│   │   ├── Actions/
│   │   ├── NotificationDropdown.php
│   │   └── Notifications.php
│   ├── Mail/                     # Mailable classes
│   │   └── UserCredentials.php
│   ├── Models/                   # Eloquent models
│   │   ├── AcademicStanding.php
│   │   ├── AcademicYear.php
│   │   ├── AccommodationType.php
│   │   ├── ActivityLog.php
│   │   ├── Announcement.php
│   │   ├── Assessment.php
│   │   ├── AssessmentType.php
│   │   ├── Attendance.php
│   │   ├── AttendanceRecord.php
│   │   ├── CalendarEvent.php
│   │   ├── Competency.php
│   │   ├── Course.php
│   │   ├── CourseFeedback.php
│   │   ├── CourseMaterial.php
│   │   ├── CourseOffering.php
│   │   ├── CoursePrerequisite.php
│   │   ├── CourseSection.php
│   │   ├── Department.php
│   │   ├── DiscussionForum.php
│   │   ├── DiscussionReply.php
│   │   ├── DiscussionTopic.php
│   │   ├── Enrollment.php
│   │   ├── ExcusedAbsence.php
│   │   ├── Faculty.php
│   │   ├── Fee.php
│   │   ├── Grade.php
│   │   ├── GradeAppeal.php
│   │   ├── GradeLockHistory.php
│   │   ├── Major.php
│   │   ├── MedicalLeave.php
│   │   ├── MedicalRecord.php
│   │   ├── Message.php
│   │   ├── Notification.php
│   │   ├── Payment.php
│   │   ├── Question.php
│   │   ├── QuizAccommodation.php
│   │   ├── QuizAttempt.php
│   │   ├── Reminder.php
│   │   ├── Semester.php
│   │   ├── StudentAccommodation.php
│   │   ├── StudentAnswer.php
│   │   ├── StudentFee.php
│   │   ├── StudentGrade.php
│   │   ├── Task.php
│   │   ├── User.php
│   │   ├── UserProfile.php
│   │   └── UserSetting.php
│   ├── Providers/                # Service providers
│   │   ├── AppServiceProvider.php
│   │   ├── FortifyServiceProvider.php
│   │   └── LangServiceProvider.php
│   ├── Services/                 # Business logic
│   │   ├── CacheService.php
│   │   ├── NotificationService.php
│   │   └── PaymentService.php
│   └── View/                     # View components
│       └── Components/
│           └── AppLayout.php
├── bootstrap/                    # Bootstrap files
├── config/                       # Configuration files
├── database/
│   ├── factories/               # Model factories
│   ├── migrations/              # Database migrations
│   └── seeders/                # Database seeders
├── lang/                        # Translation files
├── public/                      # Public assets
├── resources/
│   ├── css/                    # Stylesheets
│   ├── js/                     # JavaScript
│   └── views/                  # Blade templates
├── routes/                      # Route definitions
├── storage/                     # Storage (logs, cache, uploads)
├── tests/                       # Test files
└── vendor/                      # Composer dependencies
```

### Model Structure

All models follow Laravel conventions:

- Extend `Illuminate\Database\Eloquent\Model`
- Use `HasFactory` trait
- Define `$fillable`, `$hidden`, `$casts` properties
- Define relationships (belongsTo, hasMany, etc.)

### Controller Structure

Controllers are organized by role:

- **Admin Controllers**: Handle administrative functions
- **Teacher Controllers**: Handle instructor functions
- **Student Controllers**: Handle learner functions
- **API Controllers**: Handle API requests

---

## Contribution Guidelines

### How to Contribute

1. **Fork the Repository**

    Click the "Fork" button on the repository page.

2. **Clone Your Fork**

    ```bash
    git clone https://github.com/your-username/noor-alhuda-lms.git
    cd noor-alhuda-lms
    ```

3. **Create a Feature Branch**

    ```bash
    git checkout -b feature/your-feature-name
    ```

4. **Make Changes**

    Make your changes following the coding standards.

5. **Commit Changes**

    ```bash
    git add .
    git commit -m "Add your feature description"
    ```

6. **Push to GitHub**

    ```bash
    git push origin feature/your-feature-name
    ```

7. **Create Pull Request**

    Go to the original repository and create a pull request.

### Pull Request Process

1. Update documentation if needed
2. Ensure all tests pass
3. Update the CHANGELOG if applicable
4. Request review from maintainers
5. Address feedback promptly

### Coding Standards

- Follow PSR-12 PHP coding standard
- Use Laravel conventions
- Write meaningful commit messages
- Add comments for complex logic
- Use meaningful variable and function names

---

## Code of Conduct

### Our Pledge

We as members, contributors, and leaders pledge to make participation in our community a harassment-free experience for everyone.

### Our Standards

- Be respectful and inclusive
- Communicate professionally
- Accept constructive criticism gracefully
- Focus on what is best for the community
- Show empathy towards other community members

### Enforcement

Violations should be reported to the project team. We are committed to making community participation a positive experience for everyone.

---

## Licensing

Noor Alhuda LMS is open-source software licensed under the **MIT License**.

See [LICENSE](LICENSE) for full license text.

### What You Can Do

- Commercial use
- Modification
- Distribution
- Private use

### What You Must Do

- Include copyright notice
- Include license notice

### What You Cannot Do

- Hold developers liable for damages

---

## Versioning Policy

We use **Semantic Versioning (SemVer)**:

- **MAJOR** version: Incompatible API changes
- **MINOR** version: New functionality (backward compatible)
- **PATCH** version: Bug fixes (backward compatible)

### Version Format

`MAJOR.MINOR.PATCH`

Examples:

- `1.0.0` - Initial release
- `1.1.0` - New feature added
- `1.1.1` - Bug fix
- `2.0.0` - Breaking changes

### Release Schedule

- **Patch Releases**: As needed for bug fixes
- **Minor Releases**: Monthly or as features are completed
- **Major Releases**: As needed for significant changes

---

## Frequently Asked Questions

### General Questions

**Q: What is Noor Alhuda LMS?**
A: Noor Alhuda LMS is a comprehensive Learning Management System built with Laravel 12 and Flux UI for educational institutions.

**Q: Is this system free to use?**
A: Yes, this is open-source software under the MIT license.

**Q: What languages are supported?**
A: English, Arabic, Farsi, Turkish, French, Chinese, Indonesian, Kurdish, and Armenian.

### Technical Questions

**Q: What PHP version is required?**
A: PHP 8.2 or higher.

**Q: Which databases are supported?**
A: MySQL 8.0+ and SQLite 3.35+.

**Q: Can I use this in production?**
A: Yes, but ensure you follow security best practices and proper configuration.

### Installation Questions

**Q: Installation fails - what should I check?**
A: Verify:

- PHP version (8.2+)
- Required PHP extensions
- Database credentials
- Write permissions on storage and bootstrap/cache

**Q: How do I reset my admin password?**
A: Run: `php artisan make:admin`

**Q: How do I enable payment gateways?**
A: Configure Stripe/PayPal in your `.env` file as shown in the installation guide.

### Feature Questions

**Q: Can I add custom roles?**
A: Yes, modify the roles in the database and update the RoleMiddleware.

**Q: Can I integrate with other systems?**
A: Yes, the system can be extended with API controllers and webhooks.

---

## Support

### Getting Help

- **Documentation**: Check this README and DOC/ folder
- **Issues**: Report bugs via GitHub Issues
- **Discussions**: Use GitHub Discussions for questions

### Reporting Bugs

1. Check if issue exists
2. Provide detailed environment info
3. Include steps to reproduce
4. Add screenshots if applicable

### Feature Requests

1. Describe the feature
2. Explain use case
3. Provide examples
4. Be patient for review

---

<p align="center">Built with ❤️ for education</p>

<p align="center">© 2024 Noor Alhuda LMS. All rights reserved.</p>
