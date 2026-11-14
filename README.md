# SCMIPS Gallery Admin Panel

A secure, lightweight, and framework-free administrative dashboard for managing academic sessions, events, and gallery photographs for the **Shri Cloth Market Institute of Professional Studies (SCMIPS), Indore** website.

---

## 📁 Project Structure

```text
SCMIPS-Admin/
│
├── admin/                         # Administrative Module (Backend & Dashboard)
│   ├── assets/                    # Admin CSS & JavaScript (Bootstrap 5, FontAwesome)
│   ├── config/                    # Environment config (config.sample.php, .htaccess, web.config)
│   ├── events/                    # Event CRUD (index, create, edit, delete)
│   ├── images/                    # Multi-image upload pipeline & file deletion
│   ├── includes/                  # Core includes: db.php, auth.php, csrf.php, functions.php
│   ├── sessions/                  # Academic session CRUD (index, create, edit, delete)
│   ├── uploads/                   # Upload directory with execution prevention
│   │   └── gallery/               # Structured by {session_year}/{event_id}/
│   ├── dashboard.php              # Real-time metrics dashboard
│   ├── health.php                 # System readiness & environment checker
│   ├── index.php                  # Entry point (redirects to dashboard or login)
│   ├── login.php                  # Admin authentication handler with CSRF & security headers
│   └── logout.php                 # Session invalidation & termination
│
├── database/                      # Database Initialization & Migration Scripts
│   ├── .htaccess & web.config     # Direct HTTP access blocked (403 Forbidden)
│   ├── schema.sql                 # MySQL schema DDL & initial admin seed
│   └── seed_legacy_gallery.php    # Automated seeder for existing legacy photos
│
├── includes/                      # Shared Application & Public Integration Includes
│   ├── .htaccess & web.config     # Direct HTTP access blocked (403 Forbidden)
│   ├── db.php                     # Central PDO database connection
│   └── public_gallery_helper.php  # Public read-only helper functions (is_published = 1)
│
├── gallery.php                    # Dynamic public gallery landing page (100% design fidelity)
├── gallery-session.php            # Dynamic session/event photo viewer with simpleLightbox
│
├── reference/                     # Read-Only Reference Material (Gitignored)
│   └── scmips-website-source/     # Complete pristine snapshot of legacy website
│
├── .htaccess & web.config         # Root protection: Options -Indexes, blocks hidden & sensitive files
├── implementationplan.md          # Comprehensive technical plan & verified audit
└── README.md                      # Project documentation and deployment guide
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

## 🚀 Production Deployment Guide (cPanel / Windows Hosting)

Follow these steps for deploying to `https://scmips.in/`:

### Step 1: Pre-Deployment Backup (Mandatory)
1. Log in to cPanel on `scmips.in`.
2. Go to **Backup** or **Backup Wizard** and download a **Full Account Backup** or **Home Directory Backup**.
3. Save the downloaded archive locally as `scmips_pre_gallery_backup_YYYYMMDD.zip`.

### Step 2: Provision MySQL Database in cPanel
1. Open **MySQL Databases** in cPanel.
2. Create a new database (e.g. `scmips_gallery`).
3. Create a dedicated database user (e.g. `scmips_user`) with a strong password.
4. Add the user to the database and grant **ALL PRIVILEGES**.
5. Open **phpMyAdmin** from cPanel, select the new database, and import `database/schema.sql`.
6. *(Optional)* To populate historical sessions and 100+ legacy photos: run `php database/seed_legacy_gallery.php` via cPanel Terminal/SSH or import the generated SQL.

### Step 3: Deploy Application Files
Upload the following folders and files to `public_html/` (or `wwwroot/`):
* `admin/`
* `database/` (contains `.htaccess` preventing direct web access)
* `includes/` (contains `.htaccess` preventing direct web access)
* `gallery.php`
* `gallery-session.php`
* `.htaccess` and `web.config`

*(Do NOT upload `reference/` or `.git/`)*.

### Step 4: Configure Production Database Credentials
Create `admin/config/config.php` on the server (or copy from `config.sample.php`):
```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'scmips_gallery');
define('DB_USER', 'scmips_user');
define('DB_PASS', 'YourStrongPasswordHere');
define('APP_URL', 'https://scmips.in');
define('APP_ENV', 'production');
```
Set file permissions of `admin/config/config.php` to `640` or `600`.

### Step 5: Verify Permissions and Security
1. Ensure `admin/uploads/gallery/` is writable by the web server process (chmod `755` or Windows IIS Write permissions).
2. Confirm `admin/uploads/.htaccess` and `admin/uploads/web.config` are present to disable script execution.
3. Confirm directory browsing is disabled.

### Step 6: Post-Deployment Smoke Test
1. Visit `https://scmips.in/admin/health.php` to ensure PHP extensions (PDO, GD, fileinfo) and directory permissions are green.
2. Visit `https://scmips.in/admin/login.php` and log in (`admin` / `Admin@123`).
3. Immediately change the admin password.
4. Create an event, upload 2–3 photos, and publish.
5. Visit `https://scmips.in/gallery.php` and `https://scmips.in/gallery-session.php?year=2021-2022` to verify images and lightbox gallery render cleanly.

---

## 🔄 5-Minute Instant Rollback Strategy

If any unexpected conflict occurs on the live server:
1. Delete the uploaded `admin/` directory from `public_html/`.
2. Revert any navigation links pointing to `gallery.php` back to `gallery.html`.
3. The website immediately returns to its previous 100% static state without any downtime.

---

## 📖 Implementation Roadmap

Detailed technical specifications, verified source audit findings, and phased milestones are documented in [implementationplan.md](implementationplan.md).
