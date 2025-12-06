# Task 2 – Laravel MVC with Multiple Controller Methods

## Objective

Create a Laravel web application that demonstrates the MVC pattern by using a controller with multiple methods. Each method returns a different Blade view and passes different types of data (strings and arrays) to be displayed using Blade and loops.

## Requirements

### Controller: `HomeController`

A controller named `HomeController` with four methods:

| Method       | View Returned        | Data Passed                                       |
| ------------ | -------------------- | ------------------------------------------------- |
| `index()`    | `home.blade.php`     | `$title` - A welcome message string               |
| `about()`    | `about.blade.php`    | `$info` - A short descriptive text                |
| `features()` | `features.blade.php` | `$features` - An indexed array of feature strings |
| `team()`     | `team.blade.php`     | `$team` - An associative array of team members    |

### Routes

Routes defined in `routes/web.php`:

| URL         | Controller Method         |
| ----------- | ------------------------- |
| `/`         | `HomeController@index`    |
| `/about`    | `HomeController@about`    |
| `/features` | `HomeController@features` |
| `/team`     | `HomeController@team`     |

### Blade Views

-   **`home.blade.php`**: Displays the `$title` variable passed from the controller.
-   **`about.blade.php`**: Displays the `$info` variable passed from the controller.
-   **`features.blade.php`**: Displays an unordered list (`<ul>`) of features using `@foreach` loop.
-   **`team.blade.php`**: Displays a table of team members (name and role) using `@foreach` loop.

## Project Structure

```
app/
└── Http/
    └── Controllers/
        └── HomeController.php      # Controller with 4 methods

resources/
└── views/
    ├── home.blade.php              # Home page view
    ├── about.blade.php             # About page view
    ├── features.blade.php          # Features list view
    ├── team.blade.php              # Team table view
    └── partials/
        └── navbar.blade.php        # Reusable navigation partial

routes/
└── web.php                         # Route definitions with named routes
```

## Implementation Details

### Named Routes

Routes are defined with names for cleaner URL generation:

```php
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/features', [HomeController::class, 'features'])->name('features');
Route::get('/team', [HomeController::class, 'team'])->name('team');
```

### Blade Partials

Navigation is extracted into a reusable partial (`partials/navbar.blade.php`):

```blade
<p>
    <a href="{{ route('home') }}">Home</a> |
    <a href="{{ route('about') }}">About</a> |
    <a href="{{ route('features') }}">Features</a> |
    <a href="{{ route('team') }}">Team</a>
</p>
```

Include it in any view using:

```blade
@include('partials.navbar')
```

### HomeController Methods

```php
// index() - passes a title string
$title = "Welcome to my Laravel MVC demo!";
return view('home', compact('title'));

// about() - passes an info string
$info = "This is a simple about page...";
return view('about', compact('info'));

// features() - passes an indexed array
$features = ["Eloquent ORM", "Blade Templating", "Routing", ...];
return view('features', compact('features'));

// team() - passes an associative array
$team = [
    ['name' => 'Alice Johnson', 'role' => 'Project Manager'],
    ['name' => 'Bob Smith', 'role' => 'Backend Developer'],
    ...
];
return view('team', compact('team'));
```

### Blade Loops

**Features View (`@foreach` with indexed array):**

```blade
<ul>
    @foreach($features as $feature)
        <li>{{ $feature }}</li>
    @endforeach
</ul>
```

**Team View (`@foreach` with associative array):**

```blade
<table>
    @foreach($team as $member)
        <tr>
            <td>{{ $member['name'] }}</td>
            <td>{{ $member['role'] }}</td>
        </tr>
    @endforeach
</table>
```

## Expected Output

| URL                              | Expected Display                                        |
| -------------------------------- | ------------------------------------------------------- |
| `http://localhost:8000/`         | Title text from `index()` method                        |
| `http://localhost:8000/about`    | Descriptive text from `about()` method                  |
| `http://localhost:8000/features` | Unordered list generated from features array using loop |
| `http://localhost:8000/team`     | Table of team members generated using loop              |

## Key Concepts Demonstrated

-   **MVC Pattern**: Separation of concerns between Controller (logic) and View (presentation)
-   **Controller Methods**: Multiple actions within a single controller
-   **Data Passing**: Passing variables from controller to Blade views using `compact()`
-   **Blade Templating**: Using `{{ $variable }}` for output escaping
-   **Blade Loops**: Using `@foreach` directive to iterate over arrays
-   **Named Routes**: Using `->name()` for cleaner URL generation with `route()` helper
-   **Blade Partials**: Using `@include()` for reusable view components

## Running the Application

```bash
# Start the development server
php artisan serve

# Visit the routes in your browser:
# http://localhost:8000/
# http://localhost:8000/about
# http://localhost:8000/features
# http://localhost:8000/team
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
