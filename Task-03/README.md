# Task 03: Product Management System - CRUD Operations with Categories

This Laravel project demonstrates a complete product management system with full CRUD operations, category relationships, form validation, and a user-friendly web interface.

## Project Overview

This task covers:

-   **Models & Relationships**: `Product` and `Category` models with one-to-many relationship
-   **Database Migrations**: Creating `products` and `categories` tables with foreign key constraints
-   **Seeders**: Populating the database with categories and products
-   **Resource Controller**: Full CRUD operations via `ProductController`
-   **Form Request Validation**: `StoreProductRequest` and `UpdateProductRequest` for data validation
-   **Blade Views**: Interactive UI for listing, creating, editing, and deleting products
-   **Route Management**: Using Laravel resource routes for RESTful operations

## Database Schema

### Categories Table

| Field      | Type        | Description                  |
| ---------- | ----------- | ---------------------------- |
| id         | Primary Key | Auto-incrementing ID         |
| name       | String      | Category name                |
| created_at | Timestamp   | Record creation timestamp    |
| updated_at | Timestamp   | Record last update timestamp |

### Products Table

| Field       | Type          | Description                  |
| ----------- | ------------- | ---------------------------- |
| id          | Primary Key   | Auto-incrementing ID         |
| name        | String        | Product name                 |
| price       | Decimal(10,2) | Product price                |
| category_id | Foreign Key   | References categories.id     |
| created_at  | Timestamp     | Record creation timestamp    |
| updated_at  | Timestamp     | Record last update timestamp |

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

Populate the database with categories and products:

```bash
# Run all seeders (CategorySeeder and ProductSeeder)
php artisan db:seed

# Or run seeders individually
php artisan db:seed --class=CategorySeeder
php artisan db:seed --class=ProductSeeder
```

### 6. Fresh Migration with Seeding (Optional)

To reset and reseed the database:

```bash
php artisan migrate:fresh --seed
```

### 7. Start Development Server

Start the Laravel development server:

```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser to view the application.

## Features

### Product Management

-   **List Products**: View all products with their categories in a table
-   **Create Product**: Add new products with name, price, and category
-   **Edit Product**: Update existing product information
-   **Delete Product**: Remove products from the database
-   **Category Filtering**: Products are associated with categories

### Validation

Form requests ensure data integrity:

-   **Product Name**: Required, string, max 255 characters
-   **Price**: Required, numeric, minimum 0
-   **Category**: Required, must exist in categories table

## Routes

The application uses Laravel resource routes:

```php
Route::resource('products', ProductController::class);
```

This generates the following routes:

| Method | URI                 | Action  | Route Name       |
| ------ | ------------------- | ------- | ---------------- |
| GET    | /products           | index   | products.index   |
| GET    | /products/create    | create  | products.create  |
| POST   | /products           | store   | products.store   |
| GET    | /products/{id}/edit | edit    | products.edit    |
| PUT    | /products/{id}      | update  | products.update  |
| DELETE | /products/{id}      | destroy | products.destroy |

## Verifying Data

### Using Tinker

You can verify the seeded data using Laravel Tinker:

```bash
php artisan tinker
```

Then run:

```php
// Get all categories
App\Models\Category::all();

// Get all products with their categories
App\Models\Product::with('category')->get();

// Get products in a specific category
App\Models\Category::where('name', 'Electronics')->first()->products;
```

Expected output:

```
// Products with categories
Illuminate\Database\Eloquent\Collection {
  all: [
    App\Models\Product {
      id: 1,
      name: "Laptop",
      price: "999.99",
      category_id: 1,
      category: App\Models\Category { id: 1, name: "Electronics", ... }
    },
    // ... more products
  ],
}
```

### Using the Web Interface

1. Navigate to `http://localhost:8000` after starting the server
2. Browse the list of products
3. Click "Add New Product" to create a product
4. Click "Edit" to modify a product
5. Click "Delete" to remove a product

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── ProductController.php      # Resource controller for CRUD
│   └── Requests/
│       ├── StoreProductRequest.php    # Validation for creating products
│       └── UpdateProductRequest.php   # Validation for updating products
└── Models/
    ├── Product.php                     # Product model with category relationship
    └── Category.php                    # Category model with products relationship

database/
├── migrations/
│   ├── 2025_12_06_161500_create_categories_table.php  # Categories table
│   └── 2025_12_06_161522_create_products_table.php    # Products table with FK
└── seeders/
    ├── DatabaseSeeder.php              # Main seeder (calls all seeders)
    ├── CategorySeeder.php              # Seeds 7 categories
    └── ProductSeeder.php               # Seeds 5 products

resources/
└── views/
    └── products/
        ├── index.blade.php             # List all products
        ├── create.blade.php            # Create product form
        └── edit.blade.php              # Edit product form

routes/
└── web.php                             # Resource routes for products
```

## Seeded Data

### Categories (7)

-   Electronics
-   Fashion
-   Home & Kitchen
-   Sports & Outdoors
-   Books
-   Toys & Games
-   Health & Beauty

### Products (5)

| Name                | Price   | Category    |
| ------------------- | ------- | ----------- |
| Laptop              | $999.99 | Electronics |
| Smartphone          | $599.50 | Electronics |
| Headphones          | $149.99 | Electronics |
| Wireless Mouse      | $29.99  | Electronics |
| Mechanical Keyboard | $89.99  | Electronics |

## Model Relationships

### Product Model

```php
public function category(): BelongsTo
{
    return $this->belongsTo(Category::class);
}
```

### Category Model

```php
public function products(): HasMany
{
    return $this->hasMany(Product::class);
}
```

## Key Learning Points

1. **Eloquent Relationships**: Understanding one-to-many relationships between models
2. **Resource Controllers**: Using Laravel's resource controllers for CRUD operations
3. **Form Request Validation**: Separating validation logic into dedicated request classes
4. **Mass Assignment Protection**: Using `$fillable` to protect against mass assignment vulnerabilities
5. **Database Migrations**: Creating tables with foreign key constraints
6. **Seeders**: Populating related data (categories before products)
7. **Blade Templates**: Creating reusable views for CRUD operations
8. **Route Model Binding**: Automatically injecting models into controller methods

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
