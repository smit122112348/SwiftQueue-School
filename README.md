# Swiftqueue School

Swiftqueue School is a web application that allows users to manage courses and user roles. It includes functionality for user authentication, adding/deleting courses, and managing user access levels.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)
- [Folder Structure](#folder-structure)

## Features

- User registration and authentication
- Add, edit, and delete courses
- Filtering of courses on the basis of their Status
- Admin user management
  - Promote users to admin
  - Block/unblock users
  - Delete Basic users
- CSRF protection for form submissions
- CORS protection
- Tailwind CSS 

## Requirements

- PHP 7.4 or higher
- MySQL or MariaDB
- Web Server (eg: Apache)

## Installation

1. Clone the repository:

    ```sh
    git clone https://github.com/smit122112348/SwiftQueue-School.git
    cd swiftqueue-school
    ```

2. Create a `config.php` file in the root directory of your project. This file will store your database and CORS configuration settings.

    ```php
    <?php
    // config.php
    return [
        'DB_HOST' => 'yourhost',
        'DB_USER' => 'youruser',
        'DB_PASS' => 'yourpassword',
        'DB_NAME' => 'yourdb',
        'ALLOWED_ORIGINS' => ['http://localhost:3000', 'https://example.com'],
        'ALLOWED_METHODS' => 'GET, POST, PUT, DELETE, OPTIONS',
        'ALLOWED_HEADERS' => 'Content-Type, Authorization, X-Requested-With, X-CSRF-Token'
    ];
    ```

3. Start the web server:

    ```sh
    php -S localhost:3000
    ```

    You can now access the application at `http://localhost:3000`.

4. Initial Users:
    ```sh
    admin@admin.com : Admin@admin1
    123@gmail.com : Basic@basic1
    ```

## Usage

1. **Register and Login:**
    - Navigate to the registration page to create a new account.
    - Use your credentials to log in.

2. **Course Management:**
    - As an authenticated user, you can add new courses, edit and delete existing courses.
    - Courses can be filter viewed on the basis of their Status (Inactive, Active).

3. **User Management (Admin Only):**
    -  Can promote other users to admin. 
    -  Can block / unblock other basic users.
    -  Can delete other basic user.
    - View other user details (name, email, type).

4. **All Users**
    - Can delete their own account
    - View their own details (name, email, type). 


## Folder Structure
```
swiftqueue-school/
│
├── controllers/        # Contains the application controllers
│   └── CourseController.php
│   └── UserController.php
│
├── models/             # Contains the application models
│   └── Course.php
│   └── User.php
│
├── views/              # Contains the application views
│   └── 404.php
|   └── editCourse.php
|   └── home.php
|   └── login.php
|   └── newCourse.php
│   └── register.php
│   └── userDetails.php
|   
├── index.php       # Main entry point of the application
│
├── config.php          # Configuration file for database and CORS settings
|
├── cors.php            # CORS handling script
|
├── db.php              # Database connection setup
|
├── seeder.php          # Seeder the initial data, create table and database
|
└── README.md           # This file
```
