# SCMIPS Gallery Admin Panel

A secure, lightweight, and framework-free administrative dashboard for managing academic sessions, events, and gallery photographs for the **Shri Cloth Market Institute of Professional Studies (SCMIPS), Indore** website.

---

## 📁 Project Structure

```text
SCMIPS-Admin/
│
├── admin/                         # Administrative Module (Backend & Dashboard)
│   ├── assets/                    # Admin-specific CSS & JavaScript
│   ├── config/                    # Configuration settings (config.sample.php)
│   ├── includes/                  # Core includes: db.php, auth.php, csrf.php, functions.php
│   ├── uploads/                   # Upload directory with execution prevention
│   │   └── gallery/               # Stored image assets
│   ├── health.php                 # System readiness & environment checker
│   ├── index.php                  # Entry point (redirects to dashboard or login)
│   ├── login.php                  # Admin authentication handler
│   └── logout.php                 # Session invalidation & termination
│
├── database/                      # Database Initialization & Migration Scripts
│   └── schema.sql                 # Complete MySQL schema DDL & initial admin seed
│
├── includes/                      # Shared Application & Public Integration Includes
│
├── reference/                     # Read-Only Reference Material (Gitignored)
│   └── scmips-website-source/     # Complete pristine snapshot of legacy website
│
├── implementationplan.md          # Comprehensive step-by-step development roadmap
└── README.md                      # Project documentation and quickstart guide
```

---

## 🛠️ Technology Stack

* **Language:** Native PHP 8.x
* **Database:** MySQL (InnoDB engine, `utf8mb4_unicode_ci`)
* **DB Abstraction:** PDO (PHP Data Objects) with prepared statements
* **Frontend:** HTML5, CSS3, JavaScript, Bootstrap 5 (Admin interface)
* **Image Processing:** PHP GD Extension
* **Local Server:** XAMPP / PHP Built-in Server on Windows
* **Target Hosting:** Windows cPanel Shared Hosting (`scmips.in`)

---

## 🚀 Quickstart & Local Setup

### 1. Prerequisites
Ensure you have installed:
* [XAMPP](https://www.apachefriends.org/) with PHP 8.0+ and MySQL running.

### 2. Environment Configuration
1. Navigate to `admin/config/`:
   ```bash
   copy admin\config\config.sample.php admin\config\config.php
   ```
2. Verify or update local database credentials in `admin/config/config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'scmips_db');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('APP_URL', 'http://localhost/SCMIPS-Admin');
   ```

### 3. Database Initialization
Import the database schema into MySQL:
```bash
mysql -u root < database/schema.sql
```
This creates the database `scmips_db`, sets up the 4 relational tables (`admins`, `gallery_sessions`, `gallery_events`, `gallery_images`), and seeds the default administrator.

### 4. Health Check
Verify your local PHP runtime and extension readiness by visiting:
```text
http://localhost/SCMIPS-Admin/admin/health.php
```
Or for JSON output:
```text
http://localhost/SCMIPS-Admin/admin/health.php?format=json
```

---

## 🔐 Default Credentials

| Field | Default Value |
| :--- | :--- |
| **Username** | `admin` |
| **Password** | `Admin@123` |
| **Login URL** | `http://localhost/SCMIPS-Admin/admin/login.php` |

> *Note: Change the default password upon initial production deployment.*

---

## 🛡️ Security Architecture

* **Zero SQL Injection:** All database operations utilize PDO prepared statements with strict parameter binding (`PDO::ATTR_EMULATE_PREPARES => false`).
* **CSRF Protection:** Cryptographic CSRF tokens (`bin2hex(random_bytes(32))`) protect all POST form submissions.
* **Session Hardening:** Configured with `session.cookie_httponly = 1`, `session.use_only_cookies = 1`, and `session_regenerate_id(true)` upon successful login.
* **Strict Upload Verification:** Files are verified via binary MIME inspection (`finfo`), restricted to `jpg`, `jpeg`, `png`, and `webp`, assigned randomized filenames (`img_<hash>_<time>.<ext>`), and stored in `admin/uploads/gallery/`.
* **Execution Prevention:** Both `.htaccess` (Apache) and `web.config` (IIS) reside in `admin/uploads/` to block any script execution (`.php`, `.phtml`, `.exe`).

---

## 📖 Implementation Roadmap

Detailed technical specifications, verified source audit findings, and phased milestones are documented in [implementationplan.md](implementationplan.md).
