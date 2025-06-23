<p align="center">
  <h1 align="center">🚌 Transportation Tracking System</h1>
  <p align="center">A comprehensive Laravel-based backend system for real-time vehicle and route tracking</p>
</p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## 📋 Table of Contents

-   [About](#about)
-   [Features](#features)
-   [Requirements](#requirements)
-   [Installation](#installation)
-   [Configuration](#configuration)
-   [Database Setup](#database-setup)
-   [API Documentation](#api-documentation)
-   [Usage](#usage)
-   [Testing](#testing)
-   [Contributing](#contributing)
-   [License](#license)

## 🎯 About

The Transportation Tracking System is a robust Laravel-based backend application designed to manage and monitor transportation operations in real-time. It provides comprehensive solutions for fleet management, route optimization, driver management, and real-time vehicle tracking.

### Key Components

-   **Driver Management**: Comprehensive driver profiles with licensing and contact information
-   **Vehicle Tracking**: Real-time vehicle location and status monitoring
-   **Route Management**: Dynamic route planning with waypoints and stops
-   **Schedule Management**: Automated scheduling system with time-based operations
-   **Vendor Management**: Multi-vendor support with commission tracking
-   **Notification System**: Real-time alerts for incidents, weather, and system updates
-   **Weather Integration**: Weather-based route adjustments and alerts
-   **SOS System**: Emergency response capabilities for drivers and passengers

## ✨ Features

### 🚗 Fleet Management

-   Real-time vehicle tracking and monitoring
-   Vehicle status indicators (online, offline, maintenance)
-   Comprehensive vehicle profiles and documentation

### 👨‍✈️ Driver Management

-   Driver authentication and profile management
-   License verification and tracking
-   Performance monitoring and incident reporting

### 🗺️ Route & Schedule Management

-   Dynamic route creation with GPS waypoints
-   Automated scheduling system
-   Real-time route optimization based on traffic and weather

### 🏢 Vendor Management

-   Multi-vendor support with individual contracts
-   Commission tracking and payment management
-   Vendor performance analytics

### 🔔 Smart Notifications

-   Real-time system notifications
-   Weather-based route alerts
-   Incident and emergency notifications
-   Schedule change notifications

### 🌤️ Weather Integration

-   Real-time weather monitoring
-   Route impact analysis
-   Automatic schedule adjustments

## 📋 Requirements

-   PHP 8.2 or higher
-   Composer
-   Node.js & NPM
-   MySQL 8.0 or PostgreSQL 13+
-   Redis (for caching and queues)

## 🚀 Installation

1. **Clone the repository**

    ```bash
    git clone https://github.com/GaafarDev/tracking-system-backend
    cd tracking-system-backend
    ```

2. **Install PHP dependencies**

    ```bash
    composer install
    ```

3. **Install Node.js dependencies**

    ```bash
    npm install
    ```

4. **Environment setup**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

5. **Build assets**
    ```bash
    npm run build
    ```

## ⚙️ Configuration

### Environment Variables

Update your `.env` file with the following key configurations:

```env
# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tracking_system
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Cache & Queue Configuration
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# Mail Configuration (for notifications)
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password

# API Keys (if using external services)
WEATHER_API_KEY=your_weather_api_key
MAPS_API_KEY=your_maps_api_key
```

## 🗄️ Database Setup

1. **Run migrations**

    ```bash
    php artisan migrate
    ```

2. **Seed the database**

    ```bash
    php artisan db:seed
    ```

3. **Run vendor data migration** (if upgrading from legacy system)
    ```bash
    php artisan db:seed --class=VendorDataMigrationSeeder
    ```

### Key Database Tables

-   [`users`](database/migrations/0001_01_01_000000_create_users_table.php) - User authentication and profiles
-   [`vendors`](database/migrations/2025_06_02_080304_create_vendors_table.php) - Vendor management and contracts
-   [`notifications`](database/migrations/2025_04_03_061013_create_notifications_table.php) - System notifications
-   [`weather_updates`](database/migrations/2025_04_03_061029_create_weather_updates_table.php) - Weather data and alerts

## 📚 API Documentation

### Authentication

The system uses Laravel Sanctum for API authentication. Personal access tokens are managed through the [`personal_access_tokens`](database/migrations/2025_04_03_052907_create_personal_access_tokens_table.php) table.

### Key Models

-   [`Driver`](_ide_helper_models.php#L0) - Driver management and authentication
-   [`Vehicle`](_ide_helper_models.php) - Vehicle tracking and status
-   [`Route`](_ide_helper_models.php#L147) - Route management with waypoints
-   [`Schedule`](_ide_helper_models.php#L194) - Automated scheduling system
-   [`Notification`](_ide_helper_models.php#L132) - Real-time notifications
-   [`WeatherUpdate`](_ide_helper_models.php#L324) - Weather monitoring

## 🎮 Usage

### Starting the Development Server

```bash
# Start Laravel development server
php artisan serve

# Start Vite development server (for frontend assets)
npm run dev

# Start queue workers (for background jobs)
php artisan queue:work
```

### Common Commands

```bash
# Clear all caches
php artisan optimize:clear

# Generate IDE helper files
php artisan ide-helper:generate
php artisan ide-helper:models

# Run tests
php artisan test
```

## 🧪 Testing

Run the test suite:

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Development Guidelines

-   Follow PSR-12 coding standards
-   Write comprehensive tests for new features
-   Update documentation as needed
-   Ensure all tests pass before submitting PRs

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

-   Built with [Laravel](https://laravel.com) - The PHP Framework for Web Artisans
-   UI components styled with [Tailwind CSS](https://tailwindcss.com)
-   Frontend powered by [Vue.js](https://vuejs.org) and [Inertia.js](https://inertiajs.com)

---

<p align="center">Made with ❤️ for efficient transportation management</p>
