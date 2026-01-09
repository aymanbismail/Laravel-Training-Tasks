# Task 06: Many-to-Many Relationship (Products ↔ Suppliers) with Pivot Data

This Laravel project demonstrates a complete product management system with CRUD operations, category relationships, and a many-to-many relationship between Products and Suppliers with pivot data.

## Project Overview

This task covers:

-   **Models & Relationships**: `Product`, `Category`, and `Supplier` models
-   **One-to-Many**: Products belong to Categories
-   **Many-to-Many**: Products and Suppliers with pivot data (cost_price, lead_time_days)
-   **Database Migrations**: Tables with foreign key constraints and pivot table
-   **Seeders**: Populating categories, suppliers, products, and pivot data
-   **Resource Controller**: Full CRUD operations via `ProductController`
-   **Form Request Validation**: Validation for products and supplier pivot data
-   **Blade Views**: Interactive UI with supplier management
-   **Eager Loading**: Optimized queries with `with()` and `withCount()`

## Database Schema

### Categories Table

| Field      | Type        | Description                  |
| ---------- | ----------- | ---------------------------- |
| id         | Primary Key | Auto-incrementing ID         |
| name       | String      | Category name (unique)       |
| created_at | Timestamp   | Record creation timestamp    |
| updated_at | Timestamp   | Record last update timestamp |

### Products Table

| Field       | Type          | Description                  |
| ----------- | ------------- | ---------------------------- |
| id          | Primary Key   | Auto-incrementing ID         |
| name        | String        | Product name (unique)        |
| price       | Decimal(10,2) | Product price                |
| category_id | Foreign Key   | References categories.id     |
| created_at  | Timestamp     | Record creation timestamp    |
| updated_at  | Timestamp     | Record last update timestamp |

### Suppliers Table

| Field      | Type        | Description                  |
| ---------- | ----------- | ---------------------------- |
| id         | Primary Key | Auto-incrementing ID         |
| name       | String      | Supplier name (unique)       |
| email      | String      | Supplier email (unique)      |
| created_at | Timestamp   | Record creation timestamp    |
| updated_at | Timestamp   | Record last update timestamp |

### Pivot Table (product_supplier)

| Field          | Type          | Description                       |
| -------------- | ------------- | --------------------------------- |
| id             | Primary Key   | Auto-incrementing ID              |
| product_id     | Foreign Key   | References products.id (cascade)  |
| supplier_id    | Foreign Key   | References suppliers.id (cascade) |
| cost_price     | Decimal(10,2) | Cost price from this supplier     |
| lead_time_days | Integer       | Lead time in days                 |
| created_at     | Timestamp     | Record creation timestamp         |
| updated_at     | Timestamp     | Record last update timestamp      |

**Constraints**: Composite unique on (product_id, supplier_id)

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

-   **List Products**: View all products with categories and suppliers
-   **Create Product**: Add new products with name, price, category, and suppliers
-   **Edit Product**: Update existing product information and supplier assignments
-   **Delete Product**: Remove products (cascades to pivot table)
-   **Supplier Display**: Shows supplier count and details with pivot data (cost, lead time)

### Supplier Management

-   **Many-to-Many Relationship**: Products can have multiple suppliers and vice versa
-   **Pivot Data**: Each product-supplier relationship stores cost_price and lead_time_days
-   **Form Integration**: Checkbox selection with pivot data inputs in create/edit forms
-   **Sync Operations**: Properly handles adding, removing, and updating supplier relationships

### Validation

Form requests ensure data integrity:

-   **Product Name**: Required, string, max 255 characters, unique
-   **Price**: Required, numeric, greater than 0
-   **Category**: Required, must exist in categories table
-   **Suppliers**: At least one supplier must be selected
-   **Cost Price**: Required for selected suppliers, numeric, min 0
-   **Lead Time**: Required for selected suppliers, integer, min 0

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

// Get all suppliers
App\Models\Supplier::all();

// Get products with suppliers and pivot data
App\Models\Product::with('suppliers')->get();

// Access pivot data
$product = App\Models\Product::with('suppliers')->first();
foreach ($product->suppliers as $supplier) {
    echo $supplier->name;
    echo $supplier->pivot->cost_price;
    echo $supplier->pivot->lead_time_days;
}

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
│   │   └── ProductController.php      # Resource controller with supplier sync
│   └── Requests/
│       ├── StoreProductRequest.php    # Validation for creating products
│       └── UpdateProductRequest.php   # Validation for updating products
└── Models/
    ├── Product.php                     # Product model (belongsTo Category, belongsToMany Suppliers)
    ├── Category.php                    # Category model (hasMany Products)
    └── Supplier.php                    # Supplier model (belongsToMany Products)

database/
├── migrations/
│   ├── 2025_12_06_161500_create_categories_table.php    # Categories table
│   ├── 2025_12_06_161522_create_products_table.php      # Products table
│   ├── 2025_12_06_170000_create_suppliers_table.php     # Suppliers table
│   └── 2025_12_06_170100_create_product_supplier_table.php  # Pivot table
└── seeders/
    ├── DatabaseSeeder.php              # Main seeder (calls all seeders)
    ├── CategorySeeder.php              # Seeds 7 categories
    ├── SupplierSeeder.php              # Seeds 5 suppliers
    ├── ProductSeeder.php               # Seeds 5 products
    └── ProductSupplierSeeder.php       # Seeds pivot data (1-3 suppliers per product)

resources/
└── views/
    └── products/
        ├── index.blade.php             # List products with supplier info
        ├── create.blade.php            # Create form with supplier selection
        └── edit.blade.php              # Edit form with supplier management

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

### Suppliers (5)

| Name               | Email                  |
| ------------------ | ---------------------- |
| Tech Parts Co.     | contact@techparts.com  |
| Global Supply Inc. | sales@globalsupply.com |
| Prime Distributors | orders@primedist.com   |
| Quality Goods Ltd. | info@qualitygoods.com  |
| Express Wholesale  | wholesale@express.com  |

### Products (5)

| Name                | Price   | Category    |
| ------------------- | ------- | ----------- |
| Laptop              | $999.99 | Electronics |
| Smartphone          | $599.50 | Electronics |
| Headphones          | $149.99 | Electronics |
| Wireless Mouse      | $29.99  | Electronics |
| Mechanical Keyboard | $89.99  | Electronics |

Each product is seeded with 1-3 random suppliers with cost_price and lead_time_days.

## Model Relationships

### Product Model

```php
// One-to-Many: Product belongs to Category
public function category(): BelongsTo
{
    return $this->belongsTo(Category::class);
}

// Many-to-Many: Product has many Suppliers with pivot data
public function suppliers(): BelongsToMany
{
    return $this->belongsToMany(Supplier::class)
        ->withPivot(['cost_price', 'lead_time_days'])
        ->withTimestamps();
}
```

### Category Model

```php
public function products(): HasMany
{
    return $this->hasMany(Product::class);
}
```

### Supplier Model

```php
public function products(): BelongsToMany
{
    return $this->belongsToMany(Product::class)
        ->withPivot(['cost_price', 'lead_time_days'])
        ->withTimestamps();
}
```

## Controller Methods

### Eager Loading (Avoid N+1)

```php
// In index method - eager load relationships and count
$products = Product::with(['category', 'suppliers'])
    ->withCount('suppliers')
    ->get();
```

### Syncing Suppliers with Pivot Data

```php
// Sync suppliers with pivot data
$syncData = [];
foreach ($suppliers as $supplierId => $data) {
    if (!empty($data['selected'])) {
        $syncData[$supplierId] = [
            'cost_price' => $data['cost_price'],
            'lead_time_days' => $data['lead_time_days'],
        ];
    }
}
$product->suppliers()->sync($syncData);
```

## Form Input Structure

The supplier form uses the following naming convention:

```html
<!-- Checkbox to select supplier -->
<input type="checkbox" name="suppliers[SUPPLIER_ID][selected]" value="1" />

<!-- Pivot data inputs -->
<input type="number" name="suppliers[SUPPLIER_ID][cost_price]" />
<input type="number" name="suppliers[SUPPLIER_ID][lead_time_days]" />
```

## Key Learning Points

1. **Many-to-Many Relationships**: Using `belongsToMany()` with pivot tables
2. **Pivot Data**: Storing additional data in pivot tables with `withPivot()`
3. **Pivot Timestamps**: Tracking pivot record changes with `withTimestamps()`
4. **Sync Method**: Using `sync()` to manage many-to-many relationships
5. **Eager Loading**: Using `with()` and `withCount()` to avoid N+1 queries
6. **Cascade Deletes**: Configuring foreign keys to cascade on delete
7. **Composite Unique Constraints**: Preventing duplicate pivot records
8. **Complex Form Validation**: Validating nested array inputs for pivot data
9. **Form Request Classes**: Separating validation logic with custom rules
10. **Blade Loops**: Displaying pivot data in views

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
