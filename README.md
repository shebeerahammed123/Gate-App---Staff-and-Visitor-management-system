## Gate App - Open Source staff & visitor management system - Shebeer Ahammed

Gate App uses a MySQL database named `gate` to store administrator accounts, staff records, pupil records, staff attendance, and visitor check-in/check-out information.

### Requirements

- MySQL 8.0 or a compatible supported version
- A MySQL user with permission to create and manage the database and tables
- PHP configured with the appropriate MySQL database driver

### 1. Create the Database and Tables

Run the following SQL script using MySQL Workbench, phpMyAdmin, or the MySQL command line.

```sql
-- Create the database
CREATE DATABASE IF NOT EXISTS schoolgate
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE schoolgate;

-- Administrator accounts
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Staff records
CREATE TABLE IF NOT EXISTS staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    role VARCHAR(50)
);

-- Staff attendance and clock-in/clock-out records
CREATE TABLE IF NOT EXISTS staff_attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT NOT NULL,
    clock_in DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    clock_out DATETIME NULL,
    method VARCHAR(50),
    CONSTRAINT fk_staff_attendance_staff
        FOREIGN KEY (staff_id)
        REFERENCES staff(id)
        ON DELETE CASCADE
);

-- Visitor check-in/check-out records
CREATE TABLE IF NOT EXISTS visitors_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    company VARCHAR(255),
    reason VARCHAR(255),
    vehicle_number VARCHAR(50),
    clock_in DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    clock_out DATETIME NULL,
    method VARCHAR(100),
    photo LONGBLOB
);
```

### 2. Database Table Overview

| Table | Purpose |
|---|---|
| `admins` | Stores administrator usernames and password hashes. |
| `staff` | Stores staff details, email addresses, and roles. |
| `staff_attendance` | Records staff clock-in and clock-out times. |
| `pupil` | Records pupil information and arrival details. |
| `visitors_log` | Records visitor details, visit reasons, arrival/departure times, and optional photos. |

### 3. Configure the Database Connection

Update the application's database configuration file with the appropriate connection details.

Example PHP configuration:

```php
<?php
$host = 'localhost';
$dbname = 'schoolgate';
$username = 'your_database_user';
$password = 'your_database_password';

$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

$pdo = new PDO($dsn, $username, $password, $options);
```

Replace the example database username and password with your actual credentials. Store production credentials securely and do not commit them to source control.

### 4. Administrator Account Setup

The `admins` table stores administrator accounts. Passwords must be stored as secure hashes generated using PHP's `password_hash()` function, not as plain text.

Example:

```php
$hash = password_hash($password, PASSWORD_DEFAULT);
```

During login, verify the supplied password using:

```php
password_verify($password, $admin['password']);
```

Create the first administrator through a secure setup process or a protected account-creation script. Do not use a publicly accessible registration page to create administrators.

### 5. Attendance and Visitor Tracking

- Staff clock-in creates a record in `staff_attendance`.
- Staff clock-out updates the relevant record's `clock_out` field.
- Visitor check-in creates a record in `visitors_log`.
- Visitor check-out updates the visitor's `clock_out` field.
- Pupil arrival information is recorded in `pupil`.

The application should ensure that clock-out updates the correct active record and that duplicate active sessions are handled appropriately.

### 6. Important Notes

- The `staff_attendance` table references `staff(id)` through a foreign key.
- Deleting a staff record also deletes its attendance history because `ON DELETE CASCADE` is enabled. For audit and safeguarding purposes, consider using inactive staff records instead of deleting staff.
- The pupil table currently records arrival details only. Add a `clock_out` field and any required collection or safeguarding fields if pupil departures need to be tracked.
- The visitor `photo` field uses `LONGBLOB`. Consider storing photos in controlled file storage and retaining only a file reference in the database, with suitable access controls and retention policies.
- Add indexes and validation as required for reporting and searching.
- Back up the database regularly and restrict access to personal information.
- Use a separate, least-privilege database account for the application rather than a MySQL root account.
