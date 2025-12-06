# Task 1 – Laravel Hello World with Dynamic Name

## Objective

Create a basic Laravel web application where the homepage (`/`) displays a static message and a personalized greeting using a dynamic name.

## Requirements

1. The homepage must display the following two lines:
   - The static message: **"Hello, World!"**
   - A personalized greeting that uses a dynamic name variable, for example: **"Hello, Ali"**

2. The name must be passed from the route to the Blade view as a variable.

3. The name must **not** be hard-coded directly inside the Blade view.

4. The page must be implemented using a Blade view, not by returning a plain string from the route.

## Expected Output

Visiting `http://localhost:8000/` should display:

```
Hello, World!
Hello, Ali
```

*(or any other name stored in the variable)*

## Implementation Notes

- The name value is passed from the route to the view as a variable, not hard-coded inside the Blade file.
- Use Laravel's Blade templating engine to render the view.

## How to Run

1. Install dependencies:
   ```bash
   composer install
   ```

2. Start the development server:
   ```bash
   php artisan serve
   ```

3. Visit `http://localhost:8000/` in your browser.
