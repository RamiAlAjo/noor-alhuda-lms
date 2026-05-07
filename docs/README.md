# Noor Alhuda LMS

<p align="center">
  <a href="https://github.com/RamiAlAjo/noor-alhuda-lms/actions"><img src="https://img.shields.io/github/actions/workflow/status/RamiAlAjo/noor-alhuda-lms?label=Build" alt="Build Status"></a>
  <a href="https://github.com/RamiAlAjo/noor-alhuda-lms/releases"><img src="https://img.shields.io/github/v/release/RamiAlAjo/noor-alhuda-lms?include_prereleases&label=Version" alt="Version"></a>
  <a href="https://github.com/RamiAlAjo/noor-alhuda-lms/stargazers"><img src="https://img.shields.io/github/stars/RamiAlAjo/noor-alhuda-lms?label=Stars" alt="Stars"></a>
  <a href="https://github.com/RamiAlAjo/noor-alhuda-lms/blob/main/LICENSE"><img src="https://img.shields.io/github/license/RamiAlAjo/noor-alhuda-lms?label=License" alt="License"></a>
  <a href="https://php.net/"><img src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php&logoColor=white" alt="PHP"></a>
  <a href="https://laravel.com/"><img src="https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white" alt="Laravel"></a>
</p>

---

**Noor Alhuda LMS** is a comprehensive Learning Management System for educational institutions. Manage courses, enrollments, assessments, grades, attendance, and payments with Laravel 12 and Flux UI.

---

## 🎯 Key Features

| Category                | Features                                                                                    |
| ----------------------- | ------------------------------------------------------------------------------------------- |
| **User Management**     | Role-based access control (Admin, Teacher, Student), user profiles, settings, impersonation |
| **Course Management**   | Course offerings, sections, schedules, rooms, capacity tracking, visibility controls        |
| **Enrollment**          | Student enrollment, approval workflow, bulk enrollment, enrollment requests                 |
| **Assessment & Grades** | Quizzes, exams, assignments, grade entry, grade appeals, grade locks                        |
| **Attendance**          | Session-based attendance tracking, excused absences, medical leaves                         |
| **Content Delivery**    | Course materials, file uploads, video integration, weekly content organization              |
| **Communication**       | Announcements, discussions, notifications, messaging                                        |
| **Financial**           | Fees management, payment tracking, Stripe & PayPal integration, invoice generation          |
| **Reporting**           | Enrollment trends, course distribution, student progress, grade analytics                   |
| **AI Features**         | Capacity prediction, enrollment forecasting, automated optimization recommendations           |
| **Search**              | Global search across users, courses, announcements with real-time results                   |
| **Notifications**       | Real-time notifications with sound alerts, toast messages, and broadcasting                 |
| **Multi-language**      | English, Arabic, Farsi, Turkish, French, Chinese, Indonesian, Kurdish, Armenian support     |
| **Theme**               | Light/Dark mode, customizable accent colors, accessibility options                          |

---

## 👥 Roles & Permissions

| Role        | Description          | Key Permissions                                                |
| ----------- | -------------------- | -------------------------------------------------------------- |
| **Admin**   | System administrator | Full access to all modules, user management, settings, reports |
| **Teacher** | Instructor/Faculty   | Course management, grade entry, attendance, announcements      |
| **Student** | Learner              | View courses, take quizzes, submit assignments, view grades    |

> [!NOTE]
> Permissions are managed via **Spatie Laravel Permission** package with granular permission controls.

---

## 🛠 Tech Stack & Packages

| Component          | Technology                    |
| ------------------ | ----------------------------- |
| **Framework**      | Laravel 12.x                  |
| **PHP**            | 8.3+                          |
| **Database**       | MySQL / SQLite (development)  |
| **UI Components**  | Flux UI                       |
| **Authentication** | Laravel Fortify               |
| **Authorization**  | Spatie Laravel Permission     |
| **File Storage**   | Laravel Filesystem (local/S3) |
| **Caching**        | Laravel Cache (file/database) |
| **Charts**         | Chart.js                      |
| **Icons**          | Heroicons / Flux Icons        |
| **Payments**       | Stripe, PayPal                |

---

## 🚀 Quick Start

### Prerequisites

- PHP 8.2+
- Composer
- Node.js 18+
- MySQL or SQLite

### Installation

```bash
# Clone the repository
git clone https://github.com/RamiAlAjo/noor-alhuda-lms.git
cd noor-alhuda-lms

# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Create environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env
# For SQLite: touch database/database.sqlite
# DB_CONNECTION=sqlite
# For MySQL: configure DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD

# Configure Payment Gateways (optional)
# Stripe: Get keys from https://dashboard.stripe.com
STRIPE_ENABLED=true
STRIPE_KEY=pk_test_xxx
STRIPE_SECRET=sk_test_xxx
STRIPE_WEBHOOK_SECRET=whsec_xxx

# PayPal: Get keys from https://developer.paypal.com
PAYPAL_ENABLED=true
PAYPAL_MODE=sandbox
PAYPAL_CLIENT_ID=xxx
PAYPAL_CLIENT_SECRET=xxx

# Run migrations
php artisan migrate

# Seed database with sample data
php artisan db:seed

# Build assets
npm run build

# Start development server
php artisan serve
```

### Default Credentials

| Role    | Email                    | Password |
| ------- | ------------------------ | -------- |
| Admin   | admin@noorlms.com        | password |
| Teacher | john.smith@noorlms.com   | password |
| Student | ahmed.hassan@noorlms.com | password |

---

## 📂 Project Structure

```
noor-alhuda-lms/
├── app/
│   ├── Actions/              # Laravel Actions
│   ├── Concerns/             # Shared traits
│   ├── Events/               # Laravel events
│   ├── Helpers/              # Helper functions
│   ├── Http/
│   │   ├── Controllers/      # Controllers
│   │   │   ├── Admin/       # Admin controllers
│   │   │   ├── Student/     # Student controllers
│   │   │   └── Teacher/     # Teacher controllers
│   │   ├── Middleware/      # Custom middleware
│   │   └── Requests/        # Form requests
│   ├── Livewire/            # Livewire components
│   ├── Mail/                # Mailable classes
│   ├── Models/              # Eloquent models
│   ├── Providers/           # Service providers
│   ├── Services/            # Business logic services
│   └── View/                # View components
├── bootstrap/               # Bootstrap files
├── config/                  # Configuration files
├── database/
│   ├── factories/          # Model factories
│   ├── migrations/         # Database migrations
│   └── seeders/           # Database seeders
├── lang/                   # Translation files
├── public/                 # Public assets
├── resources/
│   ├── css/               # Stylesheets
│   ├── js/                # JavaScript
│   └── views/             # Blade templates
├── routes/                 # Route definitions
├── storage/                # Storage (logs, cache, uploads)
├── tests/                  # Test files
└── vendor/                 # Composer dependencies
```

---

## Production Deployment Notes

```bash
# Generate optimized autoload
composer install --optimize-autoloader --no-dev

# Clear and rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create symbolic link for storage
php artisan storage:link

# Set proper permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data .

# Queue worker (if using async jobs)
php artisan queue:work --daemon

# Schedule runner
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

> [!IMPORTANT]
> Ensure your `.env` file is properly configured for production with:
>
> - `APP_ENV=production`
> - `APP_DEBUG=false`
> - Secure `APP_KEY`
> - Proper database credentials

---

## 📸 Demo & Screenshots

| Module            | Description                                            |
| ----------------- | ------------------------------------------------------ |
| Admin Dashboard   | Overview with enrollment stats, charts, quick actions  |
| Course Management | Create/edit courses with sections, schedules, capacity |
| Student View      | Course materials, grades, attendance, quizzes          |
| Teacher View      | Grade entry, attendance marking, student progress      |
| Reports           | Enrollment trends, course distribution analytics       |

_Demo screenshots coming soon._

---

## 📚 Documentation

For detailed documentation, please refer to:

- [Extended README](./DOC/README.md) - Comprehensive documentation
- [Database Schema](./DATABASE.md) - Database documentation

---

## 🤝 Contributing

Contributions are welcome! Please read our [Contributing Guide](CONTRIBUTING.md) for details.

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 📧 Contact

- **Developed By**: Rami AlAjo and Noor Alhuda
- **Email**: admin@noorlms.com
- **GitHub**: https://github.com/RamiAlAjo/noor-alhuda-lms

---

<p align="center">Built with ❤️ for education</p>
