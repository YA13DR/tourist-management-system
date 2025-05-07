<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development/)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
# Tourism Management System
## Overview
Tourism Management System is a comprehensive web application built with Laravel that streamlines the management of tourism-related services. This platform integrates various tourism services including hotel bookings, restaurant reservations, tour packages, taxi services, and travel packages into a single, user-friendly interface.

## Features
### User Management
- User registration and authentication
- Role-based access control (Admin, Manager, Customer)
- User profile management
### Hotel Management
- Hotel listings with detailed information
- Room type management
- Room availability tracking
- Hotel amenities
- Booking management
### Restaurant Services
- Restaurant listings with details
- Menu categories and items
- Table reservation system
- Restaurant ratings and reviews
### Tour Management
- Tour packages with descriptions
- Tour scheduling
- Tour guide assignment
- Tour booking system
### Taxi Services
- Taxi service listings
- Vehicle type management
- Driver management
- Taxi booking system
### Travel Packages
- Comprehensive travel packages
- Package inclusions and destinations
- Package booking management
### Booking System
- Integrated booking for all services
- Booking status tracking
- Payment processing
- Cancellation management
### Rating and Review System
- User ratings for all services
- Review moderation
- Average rating calculation
## Technical Stack
- Framework : Laravel
- Database : MySQL
- Frontend : Blade templates, Bootstrap, JavaScript
- Authentication : Laravel's built-in authentication system
## Installation
1. Clone the repository:
```bash
git clone https://github.com/yourusername/Tourism-Management.git
cd Tourism-Management
 ```
```

2. Install dependencies:
```bash
composer install
npm install
 ```

3. Set up environment variables:
```bash
cp .env.example .env
php artisan key:generate
 ```

4. Configure your database in the .env file:
```plaintext
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tourism_management
DB_USERNAME=root
DB_PASSWORD=
 ```

5. Run migrations and seeders:
```bash
php artisan migrate
php artisan db:seed
 ```

6. Start the development server:
```bash
php artisan serve
 ```

## Project Structure
The project follows Laravel's MVC architecture:

- Models : Located in app/Models/
- Controllers : Located in app/Http/Controllers/
- Views : Located in resources/views/
- Routes : Defined in routes/web.php and routes/api.php
- Migrations : Located in database/migrations/
## License
This project is licensed under the MIT License - see the LICENSE file for details.
