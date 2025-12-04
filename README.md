# LMS

A modern Learning Management System built with Laravel 11 and Livewire 3.

## 🚀 Features

- **Course Management** - Create, organize, and manage online courses
- **Video Lectures** - Vimeo integration for professional video hosting
- **Live Classes** - Schedule and manage on-site/live sessions
- **Quizzes & Assignments** - Interactive assessments with auto-grading
- **Course Analytics** - Track student progress and engagement
- **Certificates** - Auto-generated PDF certificates upon completion
- **Payment Integration** - Stripe for secure course purchases
- **Event Calendar** - FullCalendar powered scheduling system
- **Multi-role System** - Students, Tutors, and Admin dashboards
- **Wishlist & Reviews** - Course ratings and bookmarking

## 🛠️ Tech Stack

| Category | Technology |
|----------|------------|
| **Backend** | Laravel 11, PHP 8.0+ |
| **Frontend** | Livewire 3, Blade, Tailwind CSS, Bootstrap 5 |
| **Build Tool** | Vite |
| **Database** | MySQL / SQLite |
| **Video** | Vimeo API |
| **Payments** | Stripe |
| **PDF** | DomPDF |
| **Calendar** | FullCalendar |

## 📋 Requirements

- PHP >= 8.0
- Composer
- Node.js & NPM
- MySQL or SQLite
- Vimeo API credentials (for video features)
- Stripe API keys (for payments)

## ⚙️ Installation

### 1. Clone the repository

```bash
git clone <repository-url>
cd lms
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install NPM dependencies

```bash
npm install
```

### 4. Environment setup

```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configure your `.env` file

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lms
DB_USERNAME=root
DB_PASSWORD=

# Vimeo API
VIMEO_CLIENT_ID=your_client_id
VIMEO_CLIENT_SECRET=your_client_secret
VIMEO_ACCESS_TOKEN=your_access_token

# Stripe
STRIPE_KEY=your_stripe_public_key
STRIPE_SECRET=your_stripe_secret_key

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=your_mail_host
MAIL_PORT=587
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="LMS"
```

### 6. Run migrations and seeders

```bash
php artisan migrate
php artisan db:seed
```

### 7. Create storage link

```bash
php artisan storage:link
```

### 8. Build assets

```bash
# Development
npm run dev

# Production
npm run build
```

## 🚀 Running the Application

### Development (All services)

Run all development services concurrently:

```bash
composer dev
```

This starts:
- Laravel development server
- Queue worker
- Laravel Pail (log viewer)
- Vite dev server

### Manual startup

```bash
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Vite
npm run dev

# Terminal 3: Queue worker (for video processing, emails)
php artisan queue:listen
```

Visit `http://localhost:8000` in your browser.

## 📁 Project Structure

```
├── app/
│   ├── Http/Controllers/     # HTTP Controllers
│   ├── Jobs/                 # Queue jobs (Vimeo processing)
│   ├── Livewire/             # Livewire components
│   │   ├── Dashboard/        # Dashboard components
│   │   ├── CourseAnalytics/  # Analytics components
│   │   └── Quizzes/          # Quiz components
│   ├── Mail/                 # Email templates
│   ├── Models/               # Eloquent models
│   └── Services/             # Business logic services
├── database/
│   ├── migrations/           # Database migrations
│   └── seeders/              # Database seeders
├── resources/
│   ├── scss/                 # SCSS stylesheets
│   └── views/
│       ├── livewire/         # Livewire views
│       └── emails/           # Email templates
└── routes/
    └── web.php               # Web routes
```

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage
```

## 🔧 Useful Commands

```bash
# Clear all caches
php artisan optimize:clear

# Run code formatter
./vendor/bin/pint

# Fresh migration with seeders
php artisan migrate:fresh --seed

# Create a new Livewire component
php artisan make:livewire ComponentName
```

## 📧 Email Features

The system sends emails for:
- Email verification
- Password reset
- Tutor approval/rejection
- Course announcements
- Certificate delivery
- Help request responses

## 👥 User Roles

| Role | Description |
|------|-------------|
| **Admin** | Full system access, user management |
| **Tutor** | Course creation, student management |
| **Student** | Course enrollment, learning |

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License.

---

**LMS** - Empowering Education Through Technology
