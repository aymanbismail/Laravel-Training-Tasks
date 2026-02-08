# Task 08: Layout + Dashboard (App Shell)

A Laravel product management system with authentication, authorization, a unified layout shell, and a dashboard with summary cards. Built across multiple training tasks (Tasks 04–08).

## Project Overview

- **Authentication**: Registration, login, logout via Laravel Breeze (Blade stack)
- **Authorization**: Product ownership — only the creator can edit/delete their products (Policy-based)
- **Unified Layout**: Shared navbar, flash messages, and consistent styling across all pages
- **Dashboard**: Summary cards (product/category/supplier counts) + latest 5 products table
- **Models & Relationships**: `Product`, `Category`, `Supplier`, `User`
- **One-to-Many**: Products → Categories, Products → Users (ownership)
- **Many-to-Many**: Products ↔ Suppliers with pivot data (cost_price, lead_time_days)
- **Database Migrations**: Tables with foreign key constraints and pivot table
- **Seeders & Factories**: For populating data and testing
- **Resource Controller**: Full CRUD for Products with authorization checks
- **Form Request Validation**: Validation for products and supplier pivot data
- **Blade Views**: All views extend `layouts.app` with `@yield('content')`
- **Eager Loading**: Optimized queries with `with()` and `withCount()`
- **Tests**: 15 Pest tests covering guest denial, owner CRUD, and 403 for non-owners

## Database Schema

### Users Table

| Field      | Type        | Description                  |
| ---------- | ----------- | ---------------------------- |
| id         | Primary Key | Auto-incrementing ID         |
| name       | String      | User's name                  |
| email      | String      | User's email (unique)        |
| password   | String      | Hashed password              |
| created_at | Timestamp   | Record creation timestamp    |
| updated_at | Timestamp   | Record last update timestamp |

### Categories Table

| Field      | Type        | Description                  |
| ---------- | ----------- | ---------------------------- |
| id         | Primary Key | Auto-incrementing ID         |
| name       | String      | Category name (unique)       |
| created_at | Timestamp   | Record creation timestamp    |
| updated_at | Timestamp   | Record last update timestamp |

### Products Table

| Field       | Type          | Description                             |
| ----------- | ------------- | --------------------------------------- |
| id          | Primary Key   | Auto-incrementing ID                    |
| name        | String        | Product name (unique)                   |
| price       | Decimal(10,2) | Product price                           |
| category_id | Foreign Key   | References categories.id                |
| user_id     | Foreign Key   | References users.id (nullable, cascade) |
| created_at  | Timestamp     | Record creation timestamp               |
| updated_at  | Timestamp     | Record last update timestamp            |

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
npm install
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
DB_DATABASE=task_03_laravel
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 3. Generate Application Key

```bash
php artisan key:generate
```

### 4. Run Migrations

```bash
php artisan migrate
```

### 5. Run Seeders

```bash
php artisan db:seed
```

### 6. Build Frontend Assets

```bash
npm run build
```

### 7. Start Development Server

```bash
php artisan serve
```

Visit `http://localhost:8000` — you'll be redirected to the login page.

## Routes

All routes (except auth routes) require authentication.

| Method | URI                      | Action   | Controller          | Route Name       |
| ------ | ------------------------ | -------- | ------------------- | ---------------- |
| GET    | /                        | redirect | —                   | —                |
| GET    | /dashboard               | index    | DashboardController | dashboard        |
| GET    | /products                | index    | ProductController   | products.index   |
| GET    | /products/create         | create   | ProductController   | products.create  |
| POST   | /products                | store    | ProductController   | products.store   |
| GET    | /products/{product}/edit | edit     | ProductController   | products.edit    |
| PUT    | /products/{product}      | update   | ProductController   | products.update  |
| DELETE | /products/{product}      | destroy  | ProductController   | products.destroy |
| GET    | /categories              | index    | CategoryController  | categories.index |
| GET    | /suppliers               | index    | SupplierController  | suppliers.index  |
| GET    | /profile                 | edit     | ProfileController   | profile.edit     |
| PATCH  | /profile                 | update   | ProfileController   | profile.update   |
| DELETE | /profile                 | destroy  | ProfileController   | profile.destroy  |

Auth routes (login, register, logout, etc.) are provided by Laravel Breeze via `routes/auth.php`.

## Features

### Authentication & Authorization (Task 07)

- **Laravel Breeze**: Register, login, logout, password reset
- **Product ownership**: `user_id` foreign key on products; auto-assigned on creation
- **ProductPolicy**: Only the product owner can edit/update/delete
- **`@can` directives**: Edit/Delete buttons only visible to the owner

### Unified Layout (Task 08)

- **Shared layout**: `layouts.app` with `@yield('content')` — all views extend it
- **Navbar**: Dashboard, Products, Categories, Suppliers links with active highlighting
- **User info**: Displays logged-in user's name/email + Logout button
- **Guest links**: Login / Register for unauthenticated users
- **Mobile nav**: Responsive collapsed nav for small screens

### Dashboard (Task 08)

- **3 summary cards**: Total Products, Total Categories, Total Suppliers with counts
- **"View All" links**: Quick navigation from each card
- **Latest 5 products table**: Name, category, price, owner, suppliers, created date

### Flash Messages (Task 08)

- **Success messages**: Green flash after product create/update/delete
- **Error messages**: Red flash for general errors
- **Displayed in layout**: All pages get flash messages automatically

### Validation Errors

- **Validation summary**: Red box listing all errors at the top of forms
- **Field-level errors**: Inline error messages below each invalid field
- **`.is-invalid` styling**: Red border on invalid inputs

### Product Management

- **List Products**: View all products with categories, suppliers, owner
- **Create Product**: Add new products with name, price, category, and suppliers
- **Edit Product**: Update existing product information (owner only)
- **Delete Product**: Remove products with confirmation (owner only)
- **Supplier Display**: Shows supplier count and details with pivot data

### Categories & Suppliers

- **Categories index**: Lists all categories with product counts
- **Suppliers index**: Lists all suppliers with product counts

## Tests

15 Pest tests covering authentication and authorization:

```bash
php artisan test
```

### Test Coverage

| Test                                                              | Description                  |
| ----------------------------------------------------------------- | ---------------------------- |
| Guest cannot view products index                                  | Redirects to login           |
| Guest cannot access product create page                           | Redirects to login           |
| Guest cannot store a product                                      | Redirects to login           |
| Guest cannot access product edit page                             | Redirects to login           |
| Guest cannot update a product                                     | Redirects to login           |
| Guest cannot delete a product                                     | Redirects to login           |
| Logged in user can access product create page                     | Returns 200                  |
| Logged in user can store a product and ownership is assigned      | Creates product with user_id |
| Logged in user can update their own product                       | Updates successfully         |
| Logged in user can delete their own product                       | Deletes successfully         |
| Logged in user cannot update another user's product               | Returns 403                  |
| Logged in user cannot delete another user's product               | Returns 403                  |
| Logged in user cannot access edit page for another user's product | Returns 403                  |

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Controller.php              # Base controller (AuthorizesRequests)
│   │   ├── DashboardController.php     # Dashboard with counts + latest products
│   │   ├── ProductController.php       # Product CRUD with authorization
│   │   ├── CategoryController.php      # Category index with product counts
│   │   ├── SupplierController.php      # Supplier index with product counts
│   │   └── ProfileController.php       # User profile management (Breeze)
│   └── Requests/
│       ├── StoreProductRequest.php     # Validation for creating products
│       └── UpdateProductRequest.php    # Validation for updating products
├── Models/
│   ├── Product.php                     # belongsTo Category/User, belongsToMany Suppliers
│   ├── Category.php                    # hasMany Products
│   ├── Supplier.php                    # belongsToMany Products
│   └── User.php                        # hasMany Products
├── Policies/
│   └── ProductPolicy.php              # update/delete: owner check
└── Providers/
    └── AppServiceProvider.php

database/
├── factories/
│   ├── CategoryFactory.php
│   ├── ProductFactory.php
│   └── UserFactory.php
├── migrations/
│   ├── create_users_table.php
│   ├── create_categories_table.php
│   ├── create_products_table.php
│   ├── create_suppliers_table.php
│   ├── create_product_supplier_table.php
│   └── add_user_id_to_products_table.php
└── seeders/
    ├── DatabaseSeeder.php
    ├── CategorySeeder.php
    ├── SupplierSeeder.php
    ├── ProductSeeder.php
    └── ProductSupplierSeeder.php

resources/views/
├── layouts/
│   └── app.blade.php                   # Unified layout (navbar, flash, styles)
├── dashboard.blade.php                 # Dashboard with cards + latest products
├── products/
│   ├── index.blade.php                 # Product list with owner/supplier info
│   ├── create.blade.php                # Create form with supplier selection
│   └── edit.blade.php                  # Edit form with supplier management
├── categories/
│   └── index.blade.php                 # Category list with product counts
├── suppliers/
│   └── index.blade.php                 # Supplier list with product counts
└── auth/                               # Breeze auth views (login, register, etc.)

routes/
├── web.php                             # All app routes (auth-protected)
└── auth.php                            # Breeze auth routes

tests/Feature/
├── ExampleTest.php
└── ProductAuthorizationTest.php        # 13 auth/authorization tests
```

## Model Relationships

```
User ──hasMany──▶ Product
Category ──hasMany──▶ Product
Product ──belongsTo──▶ Category
Product ──belongsTo──▶ User
Product ◀──belongsToMany──▶ Supplier (pivot: cost_price, lead_time_days)
```

## Key Learning Points

1. **Authentication**: Laravel Breeze for full auth scaffolding
2. **Authorization**: Policies for resource-level access control
3. **Product Ownership**: Assigning `user_id` on creation, checking on edit/delete
4. **Unified Layout**: `@extends` / `@yield` / `@section` pattern for DRY views
5. **Flash Messages**: `->with('success', ...)` in controllers, displayed in layout
6. **Dashboard**: Aggregating model counts and recent records
7. **Active Nav Links**: `request()->routeIs()` for highlighting current page
8. **Many-to-Many with Pivot**: `belongsToMany()->withPivot()->withTimestamps()`
9. **Eager Loading**: `with()` and `withCount()` to prevent N+1
10. **Pest Testing**: Feature tests for auth flows and 403 authorization checks
11. **Form Request Validation**: Separating validation with custom error messages
12. **Middleware Groups**: Protecting route groups with `auth` middleware
