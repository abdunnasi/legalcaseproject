# LegalCase Pro — Criminal Case Management System

A full-featured legal case management system built with **Vanilla PHP**, **Tailwind CSS**, and **MySQL**.

---

## Requirements

- PHP 8.1+
- MySQL 5.7+ or MariaDB 10.4+
- Apache with `mod_rewrite` enabled (or Nginx equivalent)
- PHP extensions: `pdo_mysql`, `fileinfo`, `mbstring`

---

## Installation

### 1. Clone / copy the project

```bash
# Place the project folder in your web server root
# e.g. /var/www/html/legalcase   or   C:/xampp/htdocs/legalcase
```

### 2. Create the database

```bash
mysql -u root -p < database/migrations/001_create_tables.sql
```

Or open phpMyAdmin, create a database named `legalcase_db`, then import the SQL file.

### 3. Configure the application

Edit `config/app.php` and update the database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'legalcase_db');
define('DB_USER', 'root');       // your MySQL username
define('DB_PASS', '');           // your MySQL password
```

Update the application URL to match your setup:

```php
define('APP_URL', 'http://localhost/legalcase/public');
```

### 4. Enable Apache mod_rewrite

Make sure `AllowOverride All` is set for your virtual host, then enable the module:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### 5. Set upload directory permissions

```bash
chmod -R 755 public/uploads/
```

### 6. Open in browser

Navigate to: `http://localhost/legalcase/public`

---

## Default Login Credentials

| Role  | Email                   | Password  |
|-------|-------------------------|-----------|
| Admin | admin@legalcase.ug      | password  |
| Lawyer| lawyer@legalcase.ug     | password  |
| Clerk | clerk@legalcase.ug      | password  |

> **Change all passwords immediately after first login.**

---

## Project Structure

```
legalcase/
├── app/
│   ├── Controllers/
│   │   ├── AuthController.php      # Login / logout
│   │   └── Controllers.php         # All other controllers
│   ├── Helpers/
│   │   └── helpers.php             # Global utility functions
│   ├── Middleware/
│   │   └── AuthMiddleware.php      # Session auth & role checks
│   └── Models/
│       ├── Model.php               # Base model (find, all, count, delete)
│       ├── CaseModel.php           # Cases with relations & filters
│       └── Models.php              # Client, User, Document, Hearing, Notification
├── config/
│   ├── app.php                     # App & DB constants
│   └── database.php                # PDO singleton
├── database/
│   └── migrations/
│       └── 001_create_tables.sql   # Full schema + seed data
├── public/
│   ├── .htaccess                   # URL rewriting
│   ├── index.php                   # Front controller / bootstrap
│   └── uploads/                    # Case document uploads (writable)
├── resources/
│   └── views/
│       ├── layouts/app.php         # Main layout with sidebar
│       ├── auth/                   # login.php, profile.php
│       ├── cases/                  # index, create, edit, show
│       ├── clients/                # index, create, edit, show, _form
│       ├── dashboard/index.php
│       ├── errors/                 # 403.php, 404.php
│       ├── reports/                # index, cases, hearings
│       ├── schedules/              # index, create, edit, _form
│       └── users/                  # index, create, edit, _form
├── routes/
│   └── web.php                     # All URL → controller mappings
└── storage/
    └── logs/                       # Application logs (writable)
```

---

## Role Permissions

| Feature              | Admin | Lawyer | Clerk | Staff |
|----------------------|-------|--------|-------|-------|
| View cases           | ✅    | ✅ (own)| ✅   | ✅    |
| Create/edit cases    | ✅    | ✅     | ✅    | ❌    |
| Delete cases         | ✅    | ❌     | ❌    | ❌    |
| Manage clients       | ✅    | ✅     | ✅    | ❌    |
| Upload documents     | ✅    | ✅     | ✅    | ❌    |
| Delete documents     | ✅    | ✅     | ❌    | ❌    |
| Schedule hearings    | ✅    | ✅     | ✅    | ❌    |
| Manage users         | ✅    | ❌     | ❌    | ❌    |
| View reports         | ✅    | ✅     | ✅    | ✅    |

---

## Security Features

- CSRF tokens on all POST forms
- Password hashing with `PASSWORD_DEFAULT` (bcrypt)
- Session-based authentication with inactivity timeout
- Role-based access control on every route
- Input sanitization via `clean()` and PDO prepared statements
- File upload validation (type + size)
- Audit log trail for all key actions

---

## Allowed Upload Types

`pdf`, `doc`, `docx`, `jpg`, `jpeg`, `png` — max **10 MB** per file.

To change limits, edit `config/app.php`:

```php
define('MAX_FILE_SIZE', 10 * 1024 * 1024);
define('ALLOWED_TYPES', ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png']);
```
