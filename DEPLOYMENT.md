# SCMIPS Gallery Admin Panel - Production Deployment & Handover Guide

This document is the definitive guide for deploying the **SCMIPS Gallery Admin Panel** to the live cPanel Windows hosting server at [https://scmips.in/](https://scmips.in/).

---

## 🔍 Hardcoded Links & Settings to Replace on Live Upload

Thanks to our dynamic path resolver architecture, you do **NOT** have to edit any PHP source code files. You only configure **one single configuration file** and update navigation links on existing static HTML pages:

| Location | Parameter / Code | Current Local Value | Replace With on Live Server |
| :--- | :--- | :--- | :--- |
| **`admin/config/config.php`** | `DB_NAME` | `'scmips_gallery_admin'` | Your live cPanel database name (e.g. `scmips_gallery` or `cpaneluser_gallery`) |
| **`admin/config/config.php`** | `DB_USER` | `'root'` | Your live cPanel database user (e.g. `cpaneluser_dbuser`) |
| **`admin/config/config.php`** | `DB_PASS` | `''` (empty) | Your live cPanel database user password |
| **`admin/config/config.php`** | `APP_URL` | `'http://localhost/SCMIPS-Admin'` | `'https://scmips.in'` |
| **`admin/config/config.php`** | `APP_ENV` | `'development'` | `'production'` |
| **Public Site Navigation** (`index.html`, etc.) | Header Menu Link | `<a href="gallery.html">Gallery</a>` | `<a href="gallery.php">Gallery</a>` |

> [!NOTE]
> **Static Assets (`assets/`):** 
> The helper function `get_public_asset_prefix()` automatically detects the live server's `assets/` directory at the web root and serves all styles, scripts, and logos cleanly with **zero code modifications**.

---

## 📦 What Files to Upload to the Live Server

Upload the following folders and files directly into your cPanel `public_html/` (or `wwwroot/`):

```text
public_html/
├── admin/                     <-- Entire admin folder (dashboard, CRUD modules, assets)
├── database/                  <-- Contains schema.sql & seed scripts (protected by .htaccess)
├── includes/                  <-- db.php & public_gallery_helper.php (protected by .htaccess)
├── gallery.php                <-- Dynamic public gallery landing page
├── gallery-session.php        <-- Dynamic event viewer with simpleLightbox
├── .htaccess                  <-- Root security rules (Options -Indexes, blocks hidden files)
└── web.config                 <-- IIS Windows security configuration
```

> [!CAUTION]
> **Do NOT upload:**
> * `reference/` (legacy development archive)
> * `.git/` (git repository metadata)
> * `admin/config/config.php` (created directly on the server for security)
> * `test-*.php` (temporary local test files)

---

## 🚀 Step-by-Step Live Deployment Workflow

### Step 1: Take a Pre-Deployment Full Backup (Mandatory)
1. Log in to your cPanel account at `https://scmips.in:2083` (or your host URL).
2. Go to **Backup** or **Backup Wizard**.
3. Download a **Complete Home Directory Backup** and save it locally on your computer with today's date: `scmips_backup_YYYYMMDD.zip`.

### Step 2: Create MySQL Database in cPanel
1. In cPanel, click **MySQL Databases**.
2. Under *Create New Database*, enter a name: `gallery` (full name will be e.g. `scmips_gallery`).
3. Under *MySQL Users > Add New User*, create a user (e.g. `scmips_user`) with a strong password. **Save the password securely.**
4. Under *Add User to Database*, select your user and database, click **Add**, and check **ALL PRIVILEGES**.

### Step 3: Import Database Schema & Historical Data
1. Open **phpMyAdmin** from cPanel.
2. Select your newly created database in the left sidebar.
3. Click the **Import** tab at the top.
4. Click **Choose File** and select `database/schema.sql` from your computer. Click **Go**.
5. *(Optional historical photos)*: If you want all historical events and photos pre-loaded into the database, also import `database/seed_legacy_gallery.php` or run it once via cPanel Terminal: `php database/seed_legacy_gallery.php`.

### Step 4: Upload Application Files
1. In cPanel, open **File Manager** and navigate into `public_html/`.
2. Upload the `admin/`, `database/`, `includes/`, `gallery.php`, `gallery-session.php`, `.htaccess`, and `web.config`.
3. Ensure directory permissions:
   * `admin/uploads/gallery/` must be writable by PHP (chmod `755` on Linux or Windows Modify permissions).

### Step 5: Create Production Configuration File
1. In cPanel File Manager, go to `public_html/admin/config/`.
2. Copy `config.sample.php` to `config.php` (or click **+ File** and create `config.php`).
3. Edit `config.php` with your live cPanel credentials:
   ```php
   <?php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'scmips_gallery');
   define('DB_USER', 'scmips_user');
   define('DB_PASS', 'YourStrongLivePasswordHere');
   define('DB_CHARSET', 'utf8mb4');
   define('APP_URL', 'https://scmips.in');
   define('APP_ENV', 'production');
   ```
4. Save the file and set its permissions to `640` or `600`.

### Step 6: Update Website Navigation
1. In `public_html/index.html` (and other static pages like `courses.html`, `contact.html`), find:
   ```html
   <a href="gallery.html">Gallery</a>
   ```
2. Change it to:
   ```html
   <a href="gallery.php">Gallery</a>
   ```

### Step 7: Live Smoke Test & Security Verification
1. Visit `https://scmips.in/admin/health.php` &rarr; verify all checks are green (PDO, GD, uploads directory).
2. Visit `https://scmips.in/admin/login.php` &rarr; log in with default credentials (`admin` / `Admin@123`).
3. **Change Password Immediately:** Click your name in the top-right corner &rarr; select **Profile & Password** &rarr; change your admin password.
4. Visit `https://scmips.in/gallery.php` &rarr; confirm all sessions and event cards render cleanly.
5. Click an event &rarr; verify photographs and the interactive lightbox modal open smoothly.

---

## 🔄 5-Minute Instant Rollback Plan

If any unforeseen server error occurs:
1. Revert the navigation link in `index.html` from `gallery.php` back to `gallery.html`.
2. Delete the uploaded `gallery.php` and `/admin/` folder from `public_html/`.
3. The website immediately reverts to its original static version with **zero downtime**.

---

## 📖 College Staff User Manual (Quick Handover)

| Task | Steps |
| :--- | :--- |
| **Log In** | Visit `https://scmips.in/admin/login.php` and enter your credentials. |
| **Add Academic Year** | Click **Academic Sessions** &rarr; **Add Academic Session** &rarr; Enter e.g. `2024-2025` &rarr; Save. |
| **Create Event** | Click **Events** &rarr; **Add Event** &rarr; Select session, type event title & date &rarr; Click **Create Event & Upload Photos**. |
| **Upload Photos** | On the upload page, drag & drop multiple image files or click **Choose Photos** &rarr; Click **Upload Selected Photos**. |
| **Set Cover Photo** | Click the **⭐ Star Icon** on any uploaded photo to make it the featured cover photo for that event. |
| **Publish to Public Site** | In **Events**, click the edit icon &rarr; toggle **Publication Status** to **Published** &rarr; Save. |
| **Delete Photo** | Click the **Trash Can Icon** on any photo to remove it from the gallery and server disk. |
