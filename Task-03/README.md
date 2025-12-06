# Task 03: Basic Database Operations - Model, Migration, and Seeder

This Laravel project demonstrates basic database operations including creating a Product model, defining a migration schema, and using seeders to populate the database with dummy data.

## Project Overview

This task covers:

-   Creating the `Product` model with mass assignment protection
-   Defining the `products` table schema via migration
-   Using `ProductSeeder` to insert dummy product data
-   Verifying data using Laravel Tinker

## Database Schema

The `products` table contains the following fields:

| Field      | Type          | Description                  |
| ---------- | ------------- | ---------------------------- |
| id         | Primary Key   | Auto-incrementing ID         |
| name       | String        | Product name                 |
| price      | Decimal(10,2) | Product price                |
| created_at | Timestamp     | Record creation timestamp    |
| updated_at | Timestamp     | Record last update timestamp |

## Installation & Setup

### 1. Install Dependencies

```bash
composer install
```

### 2. Configure Environment

Copy the `.env.example` file to `.env` and configure your database settings:

```bash
cp .env.example .env
```

Update the database configuration in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_03
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 3. Generate Application Key

```bash
php artisan key:generate
```

### 4. Run Migrations

Create the database tables:

```bash
php artisan migrate
```

### 5. Run Seeders

Populate the database with dummy products:

```bash
# Run only the ProductSeeder
php artisan db:seed --class=ProductSeeder

# Or run all seeders (includes ProductSeeder)
php artisan db:seed
```

### 6. Fresh Migration with Seeding (Optional)

To reset and reseed the database:

```bash
php artisan migrate:fresh --seed
```

## Verifying Data

### Using Tinker

You can verify the seeded data using Laravel Tinker:

```bash
php artisan tinker
```

Then run:

```php
App\Models\Product::all();
```

Expected output:

```
Illuminate\Database\Eloquent\Collection {
  all: [
    App\Models\Product { id: 1, name: "Laptop", price: "999.99", ... },
    App\Models\Product { id: 2, name: "Smartphone", price: "599.50", ... },
    App\Models\Product { id: 3, name: "Headphones", price: "149.99", ... },
  ],
}
```

## Project Structure

```
app/
└── Models/
    └── Product.php          # Product model with $fillable property

database/
├── migrations/
│   └── 2025_xx_xx_create_products_table.php  # Products table schema
└── seeders/
    ├── DatabaseSeeder.php   # Main seeder (calls ProductSeeder)
    └── ProductSeeder.php    # Seeds 3 dummy products
```

## Dummy Products Seeded

| Name       | Price   |
| ---------- | ------- |
| Laptop     | $999.99 |
| Smartphone | $599.50 |
| Headphones | $149.99 |

---

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

-   [Simple, fast routing engine](https://laravel.com/docs/routing).
-   [Powerful dependency injection container](https://laravel.com/docs/container).
-   Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
-   Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
-   Database agnostic [schema migrations](https://laravel.com/docs/migrations).
-   [Robust background job processing](https://laravel.com/docs/queues).
-   [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

-   **[Vehikl](https://vehikl.com)**
-   **[Tighten Co.](https://tighten.co)**
-   **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
-   **[64 Robots](https://64robots.com)**
-   **[Curotec](https://www.curotec.com/services/technologies/laravel)**
-   **[DevSquad](https://devsquad.com/hire-laravel-developers)**
-   **[Redberry](https://redberry.international/laravel-development)**
-   **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
