# Hausa to English Translator

A web app for translating Hausa words and sentences into English, built with
PHP, MySQL, HTML, CSS, and JavaScript.

## Features

- **Public translator** — anyone can type a Hausa word or sentence and get an
  instant English translation, and browse/search the dictionary.
- **Admin account** (username `admin`, password `admin`) — logs in to upload
  the Excel/CSV dataset once (stored permanently in MySQL), view every entry,
  delete entries, and see a list of contributors.
- **Contributor accounts** — anyone can sign up, log in, and add new Hausa
  words with their English meaning. Every word they add is saved permanently
  under their account, so their contribution history is always there when
  they log back in.
- A small original emblem (sun/star burst with two interlacing threads) is
  used as the site's logo, symbolizing two languages being woven together.

## Requirements

- PHP 8.0+ with the `pdo_mysql` and `zip` extensions enabled (both are on by
  default in most PHP installs / XAMPP / WAMP).
- MySQL or MariaDB.

## Setup

1. **Create the database.**
   Import `database/schema.sql` — e.g. in phpMyAdmin, or from a terminal:
   ```
   mysql -u root -p < database/schema.sql
   ```

2. **Set your database credentials.**
   Open `config.php` and update `DB_HOST`, `DB_USER`, and `DB_PASS` if they're
   different from the defaults (`localhost` / `root` / empty password).

3. **Create the admin account.**
   Visit `setup/create_admin.php` once in your browser (or run it with
   `php setup/create_admin.php` from the command line). This creates the
   admin account with a securely hashed password. **Delete this file
   afterwards** so no one else can run it again.

4. **Serve the project from your web root.**
   The app assumes it's reachable at your domain's root (links like
   `/index.html`, `/admin/dashboard.php` are root-relative). If you're using
   XAMPP/WAMP, put the folder inside `htdocs`/`www` and browse to
   `http://localhost/hausa-translator/`, or point your virtual host / web
   server's document root directly at this folder.

5. **Log in.**
   - Admin: go to "Admin" in the nav bar, use `admin` / `admin`.
   - Contributor: go to "Contributor", sign up with any username/password.

## Project structure

```
config.php                 Database credentials
database/schema.sql        Table definitions (users, words)
setup/create_admin.php     One-time script to seed the admin account
includes/db.php            PDO connection
includes/auth.php          Session helpers (login, logout, role checks)
includes/functions.php     CSV/XLSX dataset parsing
includes/header.php        Shared page header + navigation
includes/footer.php        Shared page footer
assets/css/style.css       All styling
assets/js/app.js           Translate + search AJAX calls
assets/logo.svg            Site emblem
api/translate.php          JSON endpoint: translate a word/sentence
api/search.php             JSON endpoint: search the dictionary
api/session.php            JSON endpoint: current login status (for index.html)
api/stats.php              JSON endpoint: dataset count + last updated (for index.html)
index.html                 Public translate + browse page (static HTML)
admin_login.php            Admin login form
contributor_auth.php       Contributor sign up / log in
logout.php                 Ends the session
admin/dashboard.php         Upload dataset / all entries / contributors
admin/upload_handler.php    Imports an uploaded dataset into MySQL
admin/delete_entry.php      Deletes one dictionary entry
contributor/add_word.php    Add a word + view your own contribution history
```

## Notes

- Passwords are hashed with PHP's `password_hash()`/`password_verify()` —
  never stored in plain text.
- The `.xlsx` reader is a small built-in parser (reads the file's internal
  XML directly) so no Composer install is required. It handles simple
  two-column word lists well. For very large or heavily formatted workbooks,
  consider installing `phpoffice/phpspreadsheet` via Composer instead and
  swapping it into `includes/functions.php`.
- There's no CSRF protection or rate-limiting on the forms — fine for a
  class project or internal tool, but worth adding before any public,
  production deployment.

---
Under Supervision of Mr. MMMM, development by IB Sallau.
