# Badge Printing and Scanning Module

A comprehensive Laravel application for managing badge printing and scanning with admin and operator roles.

## Features

### Admin Side
- **Category Management**: Create and manage badge categories (Delegate, Visitor, etc.)
- **Badge Display Settings**: Configure which fields appear on badges for each category
- **Drag-and-Drop Layout Editor**: Visually design badge layouts with positioning, fonts, and styling

### Operator Side
- **Badge Search & Print**: Search by Registration ID and automatically print badges
- **Onsite Registration**: Register new attendees and print their badges immediately
- **QR Code Generation**: Automatic QR code generation with Registration ID

## Installation

1. Clone the repository
2. Install dependencies:
   ```bash
   composer install
   npm install
   ```

3. Set up environment:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Run migrations:
   ```bash
   php artisan migrate
   ```

5. Create users (passwords are automatically hashed):
   
   **Option 1: Using Seeder (Recommended)**
   ```bash
   php artisan db:seed --class=UserSeeder
   ```
   This creates:
   - Admin: admin@example.com / admin123
   - Operator: operator@example.com / operator123
   
   **Option 2: Using Artisan Command**
   ```bash
   php artisan user:create "Admin User" admin@example.com admin123 --role=admin
   php artisan user:create "Operator User" operator@example.com operator123 --role=operator
   ```
   
   **Option 3: Using Tinker (passwords auto-hashed)**
   ```bash
   php artisan tinker
   ```
   Then run:
   ```php
   \App\Models\User::create([
       'name' => 'Admin User',
       'email' => 'admin@example.com',
       'password' => 'admin123', // Automatically hashed!
       'role' => 'admin'
   ]);
   ```
   
   **Option 4: Direct Database Insert**
   You can also insert users directly into the database with plain text passwords.
   When they login, the system will automatically hash and update the password.

6. Start the server:
   ```bash
   php artisan serve
   ```

## Usage

### Admin Workflow
1. Login as admin
2. Create categories (e.g., Delegate, Visitor)
3. Configure badge display settings for each category
4. Design badge layouts using the drag-and-drop editor

### Operator Workflow
1. Login as operator
2. Search for existing registrations by RegID and print
3. Or use onsite registration to register new attendees and print badges

## Database Structure

- **categories**: Category definitions with badge dimensions
- **user_details**: User registration data
- **badge_display_settings**: Field visibility settings per category
- **badge_layout_settings**: Position and styling for each field per category

## Password Security

- **Automatic Hashing**: Passwords are automatically hashed when creating users through the model
- **Auto-Update on Login**: If a user has a plain text password in the database, it will be automatically hashed and updated when they login
- **Backend Database**: You can insert users directly with plain text passwords - they'll be hashed on first login

## Technologies

- Laravel 12
- PHP 8.2+
- SQLite (default, can be changed to MySQL/PostgreSQL)
- Simple QR Code package
- Comfortaa font for modern UI

## Design

- Minimalistic blue theme
- Comfortaa font throughout
- Responsive design
- UX-friendly with tooltips and hover effects
- Print-optimized badge layouts
