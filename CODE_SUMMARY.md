# 📝 Complete Code Summary - Laravel CRUD

## Overview

This document contains complete code for all essential files in the Laravel CRUD application.

---

## 1. User Model (`app/Models/User.php`)

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * Only these fields can be set using User::create() or $user->update()
     * This protects against mass assignment vulnerabilities
     */
    protected $fillable = [
        'name',      // User's full name
        'email',     // User's email address
        'phone',     // User's phone number
        'address',   // User's address
    ];

    /**
     * Get the attributes that should be cast.
     * Defines how data types should be handled
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
```

**Explanation**:

- `$fillable`: Lists which fields can be mass-assigned (security feature)
- `casts()`: Defines data type casting for attributes
- `created_at` & `updated_at`: Automatic timestamps managed by Laravel

---

## 2. User Controller (`app/Http/Controllers/UserController.php`)

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of all users with pagination
     * Route: GET /users
     */
    public function index()
    {
        // Get users with pagination (10 per page)
        $users = User::paginate(10);

        // Pass data to view
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user
     * Route: GET /users/create
     */
    public function create()
    {
        // Return the create form view
        return view('users.create');
    }

    /**
     * Store a newly created user in the database
     * Route: POST /users
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'name' => 'required|string|max:255',        // Required, string, max 255 chars
            'email' => 'required|email|unique:users',   // Required, valid email, unique
            'phone' => 'required|string|max:20',        // Required, string, max 20 chars
            'address' => 'required|string|max:500',     // Required, string, max 500 chars
        ]);

        // Create the user with validated data
        User::create($validated);

        // Redirect to users list with success message
        return redirect()->route('users.index')
                         ->with('success', 'User created successfully!');
    }

    /**
     * Display a specific user
     * Route: GET /users/{id}
     */
    public function show(User $user)
    {
        // Route Model Binding automatically fetches the user by ID
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing a user
     * Route: GET /users/{id}/edit
     */
    public function edit(User $user)
    {
        // Route Model Binding: $user is automatically fetched from database
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user in the database
     * Route: PUT/PATCH /users/{id}
     */
    public function update(Request $request, User $user)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // 'unique:users,email,' . $user->id allows same email for current user
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
        ]);

        // Update the user with validated data
        $user->update($validated);

        // Redirect to users list with success message
        return redirect()->route('users.index')
                         ->with('success', 'User updated successfully!');
    }

    /**
     * Remove a user from the database
     * Route: DELETE /users/{id}
     */
    public function destroy(User $user)
    {
        // Delete the user
        $user->delete();

        // Redirect to users list with success message
        return redirect()->route('users.index')
                         ->with('success', 'User deleted successfully!');
    }
}
```

**Key Concepts**:

- **Route Model Binding**: `User $user` parameter automatically fetches user by ID
- **Validation**: `validate()` method throws exception if validation fails
- **Flash Messages**: `with()` passes data to next request
- **Redirect**: `redirect()->route()` goes to named route

---

## 3. Routes (`routes/web.php`)

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

// Home route - redirects to users list
Route::get('/', function () {
    return redirect()->route('users.index');
});

/**
 * Resource Route
 * Automatically creates 7 routes for CRUD operations:
 *
 * GET     /users                → index()   - List all users
 * GET     /users/create         → create()  - Show create form
 * POST    /users                → store()   - Save new user
 * GET     /users/{id}           → show()    - Show user details
 * GET     /users/{id}/edit      → edit()    - Show edit form
 * PUT     /users/{id}           → update()  - Update user
 * DELETE  /users/{id}           → destroy() - Delete user
 */
Route::resource('users', UserController::class);
```

**Benefits of `Route::resource()`**:

- Auto-generates 7 RESTful routes
- Follows HTTP conventions (GET, POST, PUT, DELETE)
- Clean and DRY approach

---

## 4. Main Layout (`resources/views/layouts/app.blade.php`)

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'User Management') - Laravel CRUD</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f5f5f5;
            padding-top: 20px;
        }
        .navbar {
            background-color: #2c3e50;
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        .container {
            background-color: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
            margin-bottom: 30px;
        }
        h1 {
            color: #2c3e50;
            margin-bottom: 30px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('users.index') }}">
                👥 User Management System
            </a>
            <span class="navbar-text text-light">
                Laravel CRUD Application
            </span>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container">
        <!-- Display Validation Errors -->
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Validation Errors!</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Display Success Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                ✓ {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Page Content -->
        @yield('content')
    </div>

    <!-- Footer -->
    <footer class="bg-light text-center py-4 mt-5">
        <p class="text-muted mb-0">&copy; 2026 Laravel CRUD Application. Built with Laravel 13.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Delete Confirmation -->
    <script>
        function confirmDelete(userName) {
            return confirm(`Are you sure you want to delete "${userName}"? This action cannot be undone.`);
        }
    </script>
</body>
</html>
```

**Key Features**:

- `@yield('title')`: Allows child views to set page title
- `@yield('content')`: Where page content is inserted
- `@if ($errors->any())`: Display validation errors
- `session('success')`: Display success messages
- Responsive Bootstrap 5 layout

---

## 5. Index View (`resources/views/users/index.blade.php`)

```blade
@extends('layouts.app')

@section('title', 'Users List')

@section('content')
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Users List</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('users.create') }}" class="btn btn-primary btn-custom">
                <i class="bi bi-plus-circle"></i> Add New User
            </a>
        </div>
    </div>

    @if ($users->count() > 0)
        <!-- Users Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 5%">ID</th>
                        <th style="width: 20%">Name</th>
                        <th style="width: 25%">Email</th>
                        <th style="width: 15%">Phone</th>
                        <th style="width: 25%">Address</th>
                        <th style="width: 10%; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td><strong>{{ $user->id }}</strong></td>
                            <td>{{ $user->name }}</td>
                            <td>
                                <a href="mailto:{{ $user->email }}" class="text-decoration-none">
                                    {{ $user->email }}
                                </a>
                            </td>
                            <td>{{ $user->phone }}</td>
                            <td>{{ Str::limit($user->address, 30) }}</td>
                            <td style="text-align: center;">
                                <!-- Edit Button -->
                                <a href="{{ route('users.edit', $user->id) }}"
                                   class="btn btn-sm btn-warning btn-custom"
                                   title="Edit">
                                    ✏️ Edit
                                </a>

                                <!-- Delete Form with Confirmation -->
                                <form action="{{ route('users.destroy', $user->id) }}"
                                      method="POST"
                                      style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-sm btn-danger btn-custom"
                                            onclick="return confirmDelete('{{ $user->name }}');"
                                            title="Delete">
                                        🗑️ Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="d-flex justify-content-center">
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    @else
        <!-- Empty State -->
        <div class="no-data">
            <h3 class="text-muted mb-3">📭 No Users Found</h3>
            <p class="text-muted mb-3">There are no users in the system yet.</p>
            <a href="{{ route('users.create') }}" class="btn btn-primary btn-custom">
                Create First User
            </a>
        </div>
    @endif
@endsection
```

**Key Blade Directives**:

- `@extends()`: Extends the layout
- `@section()`: Defines a section to be inserted into layout
- `@foreach()`: Loop through users
- `{{ }}`: Echo (output) value
- `@csrf`: CSRF token for security
- `@method()`: HTTP method override
- `@if/@else/@endif`: Conditional rendering

---

## 6. Create View (`resources/views/users/create.blade.php`)

```blade
@extends('layouts.app')

@section('title', 'Add New User')

@section('content')
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h1>Add New User</h1>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <!-- Create User Form -->
                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf  <!-- CSRF Protection -->

                        <!-- Name Field -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   placeholder="Enter full name"
                                   value="{{ old('name') }}"
                                   required>
                            @error('name')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Email Field -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   id="email"
                                   name="email"
                                   placeholder="Enter email address"
                                   value="{{ old('email') }}"
                                   required>
                            @error('email')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Phone Field -->
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   id="phone"
                                   name="phone"
                                   placeholder="Enter phone number"
                                   value="{{ old('phone') }}"
                                   required>
                            @error('phone')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Address Field -->
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror"
                                      id="address"
                                      name="address"
                                      rows="3"
                                      placeholder="Enter address"
                                      required>{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2 justify-content-between">
                            <a href="{{ route('users.index') }}" class="btn btn-secondary btn-custom">
                                ← Back
                            </a>
                            <button type="submit" class="btn btn-success btn-custom">
                                ✓ Create User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
```

**Important Features**:

- `{{ route('users.store') }}`: Generates POST /users URL
- `@csrf`: Adds CSRF token for security
- `old('name')`: Re-populates field if validation fails
- `@error()`: Displays field-specific errors
- `is-invalid` class: Bootstrap validation styling

---

## 7. Edit View (`resources/views/users/edit.blade.php`)

```blade
@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h1>Edit User</h1>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <!-- Edit User Form -->
                    <form action="{{ route('users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')  <!-- HTTP Method Override -->

                        <!-- Name Field -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   placeholder="Enter full name"
                                   value="{{ old('name', $user->name) }}"
                                   required>
                            @error('name')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Email Field -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   id="email"
                                   name="email"
                                   placeholder="Enter email address"
                                   value="{{ old('email', $user->email) }}"
                                   required>
                            @error('email')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Phone Field -->
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   id="phone"
                                   name="phone"
                                   placeholder="Enter phone number"
                                   value="{{ old('phone', $user->phone) }}"
                                   required>
                            @error('phone')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Address Field -->
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror"
                                      id="address"
                                      name="address"
                                      rows="3"
                                      placeholder="Enter address"
                                      required>{{ old('address', $user->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2 justify-content-between">
                            <a href="{{ route('users.index') }}" class="btn btn-secondary btn-custom">
                                ← Back
                            </a>
                            <button type="submit" class="btn btn-success btn-custom">
                                ✓ Update User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
```

**Key Differences from Create**:

- `@method('PUT')`: Overrides POST to PUT for REST convention
- `{{ route('users.update', $user->id) }}`: Points to update route
- `old('name', $user->name)`: Shows existing data with fallback
- Pre-fills all fields with current user data

---

## 8. Migration (`database/migrations/2024_05_04_000003_add_phone_address_to_users_table.php`)

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds phone and address columns to users table
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add phone column after email
            $table->string('phone')->nullable()->after('email');

            // Add address column after phone
            $table->string('address')->nullable()->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     * Removes the columns if rolled back
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone');
            $table->dropColumn('address');
        });
    }
};
```

**Migration Concepts**:

- `up()`: Run when migrating forward
- `down()`: Run when rolling back
- `nullable()`: Allows NULL values
- `after()`: Positions column after specified column

---

## 9. Environment Configuration (`.env`)

```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:FFLAcYztvXx83/nT4MGJSPD1+ZZhPBSfdcSu4wBI1uY=
APP_DEBUG=true
APP_URL=http://localhost

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_crud
DB_USERNAME=root
DB_PASSWORD=

# Session Configuration
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false

# Cache Configuration
CACHE_STORE=database

# Queue Configuration
QUEUE_CONNECTION=database

# Mail Configuration
MAIL_MAILER=log
```

**Key Settings**:

- `APP_DEBUG=true`: Show detailed errors (set to false in production)
- `DB_*`: Database connection details
- `SESSION_DRIVER=database`: Store sessions in database
- `CACHE_STORE=database`: Use database for caching

---

## Summary of Data Flow

```
1. User visits /users
   ↓
2. Route directs to UserController@index()
   ↓
3. Controller queries database: User::paginate(10)
   ↓
4. Returns view with $users data
   ↓
5. Blade template (index.blade.php) renders HTML
   ↓
6. User sees list of all users in table format

7. User clicks "Edit"
   ↓
8. Goes to /users/{id}/edit
   ↓
9. Controller shows form with current user data
   ↓
10. User modifies data and submits form (PUT /users/{id})
    ↓
11. Controller validates and updates in database
    ↓
12. Redirects to /users with success message
```

---

This complete code provides a production-ready CRUD application following Laravel best practices!
