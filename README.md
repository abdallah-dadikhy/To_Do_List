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

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# ELKOOD To-Do List RESTful API

## Overview

This project implements a professional RESTful API for managing daily tasks (To-Do List) as per ELKOOD's Backend Task requirements. It focuses on a scalable architecture, clean code practices, and robust functionality, including user management with role-based access control.

## Technologies Used

* **Backend Framework:** Laravel (PHP)
* **Authentication:** Laravel Sanctum (for JWT-based API token authentication)
* **Database:** MySQL (Configured for SQLite in testing)
* **Database ORM:** Eloquent (Laravel's ORM) with Migrations & Seeders
* **Architecture:**
    * Repository Pattern for data access abstraction
    * Dependency Injection (DI)
    * Data Transfer Objects (DTOs) using `spatie/laravel-data` for input validation and output mapping.
* **Error Handling & Logging:** Global error handling configured to return JSON responses for API errors, with robust logging.
* **Development & Deployment:** Docker (Dockerfile & Docker Compose) - *Note: Docker setup is provided but requires local Docker installation.*
* **Testing:** PHPUnit (for Unit and Feature/Integration Tests)

## Features

### User Management
* **Registration:** Create new `guest` users.
* **Login:** Authenticate users and issue API tokens.
* **Logout:** Revoke active API tokens.
* **User Details:** Retrieve authenticated user's own details.
* **Owner Management:**
    * View all users.
    * Create (invite) new users (as `owner` or `guest`).
    * Update user details (name, email, password, role).
    * Delete users.
    * *Role-Based Access Control*: Only `owner` users have full management capabilities. `Owner` cannot change their own role or delete their own account.

### Task Management
* **CRUD Operations:** Create, Retrieve (all, specific), Update, Delete tasks.
* **Completion Status:** Mark tasks as completed or incomplete.
* **Filtering & Searching:** Support for filtering tasks by completion status, priority, and category. Search by title or description.
* **Pagination:** Support for paginating task lists.
* **Categorization:** Tasks can be associated with categories and priorities.
* *Role-Based Access Control*:
    * `Owner` users have full CRUD access to their tasks.
    * `Guest` users can only create tasks, view their tasks, and update the `is_completed` status of their own tasks. They cannot update other task fields or delete tasks.

## Prerequisites

Before running this project, ensure you have the following installed:

* **PHP:** 8.1 or higher
* **Composer:** Latest stable version
* **Database:** MySQL (recommended) or PostgreSQL. For local development and testing, SQLite is also supported.
* **Docker Desktop:** (Recommended for easy setup) - Includes Docker Engine and Docker Compose.
* **Git**

## Getting Started

Follow these steps to get your development environment up and running:

### 1. Clone the Repository

```bash
git clone <URL_OF_YOUR_REPOSITORY>
cd your-project-name

## API Testing

You can use the provided Postman Collection or a tool like Insomnia to test the API endpoints.

### Postman Collection
* **File:** `ELKOOD_ToDo_API.json` (This file is located in the root of the repository.)
* **Instructions:**
    1.  Import the `ELKOOD_ToDo_API.json` file into Postman.
    2.  Set up an environment variable in Postman (e.g., `baseUrl` = `http://localhost:8000/api`).
    3.  Follow the request order (e.g., register/login first to obtain a `Bearer Token`).
    4.  Set the `Authorization` header with `Bearer {{token}}` for protected routes.