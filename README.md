# Swiftqueue School

Swiftqueue School is a web application that allows users to manage courses and user roles. It includes functionality for user authentication, adding/deleting courses, and managing user access levels.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)
- [Folder Structure](#folder-structure)
- [Contributing](#contributing)
- [License](#license)

## Features

- User registration and authentication
- Add, edit, and delete courses
- Admin user management
  - Promote users to admin
  - Block/unblock users
- CSRF protection for form submissions

## Requirements

- PHP 7.4 or higher
- MySQL or MariaDB
- Composer
- Web server (e.g., Apache, Nginx)

## Installation

1. Clone the repository:

    ```sh
    git clone https://github.com/yourusername/swiftqueue-school.git
    cd swiftqueue-school
    ```

2. Install dependencies using Composer:

    ```sh
    composer install
    ```

3. Configure the database:

    - Create a new MySQL database.
    - Update the database configuration in `db.php`:

        ```php
        <?php
        return new PDO('mysql:host=localhost;dbname=yourdbname', 'yourusername', 'yourpassword');
        ```

4. Run the database migrations to set up the tables:

    ```sh
    php migrate.php
    ```

5. Start the web server:

    ```sh
    php -S localhost:8000
    ```

    You can now access the application at `http://localhost:8000`.

## Usage

1. **Register and Login:**
    - Navigate to the registration page to create a new account.
    - Use your credentials to log in.

2. **Course Management:**
    - As an authenticated user, you can add new courses.
    - As an admin, you can edit or delete any course.

3. **User Management (Admin Only):**
    - Admin users can promote other users to admin or block/unblock users.