# Lunera Evleri — Site Management System

A PHP and MySQL web application developed during a software internship to explore residential property management workflows.

> **Portfolio / learning project.** This repository is not production-ready. Review security and authorization before deploying it to a public server.

## Features

- Resident registration and login
- Apartment and monthly dues management
- Expense tracking and reporting
- Maintenance and issue records
- Employee, visitor, asset and booking pages
- Email integration (requires local SMTP configuration)

## Technology

PHP 8, MySQL / MariaDB, PDO, HTML, CSS, Bootstrap 5, JavaScript, Chart.js and PHPMailer.

## Project structure

```text
site_management/
  api/            PHP endpoints
  includes/       Database connection
  public/         Web pages
  dashboard.php   Dashboard entry
  logout.php      Logout
 database/
   schema.sql     Database structure only (no user records)
```

## Local setup (Windows / XAMPP)

1. Install XAMPP with Apache, PHP and MySQL/MariaDB.
2. Copy the `site_management` folder to `C:\xampp\htdocs\site_management`.
3. Start Apache and MySQL from the XAMPP Control Panel.
4. In `http://localhost/phpmyadmin`, create a database named `site_management` (utf8mb4) and import `database/schema.sql` into an **empty** database.
5. By default, the database connector uses the typical local XAMPP account (`root` with an empty password). For other setups, configure `DB_HOST`, `DB_NAME`, `DB_USER`, and `DB_PASSWORD` as OS/Apache environment variables. `.env.example` is documentation, not an automatically loaded `.env` file.
6. Open `http://localhost/site_management/public/users.php` to register a standard user, then sign in. Some administration functions require an administrator role; provision admin access only in a trusted local environment.
7. Email requires `SMTP_USERNAME`, `SMTP_PASSWORD` and optionally `SMTP_FROM` environment variables. Do not commit real credentials.

## Notes and limitations

- The SQL file includes table structures only; original database records are deliberately excluded.
- Some legacy PHP endpoints and pages have not undergone a full security review. Do not expose this application to the public internet.
- Public registration now assigns a regular user role; the old self-assigned admin role was unsafe.
- Some pages may rely on existing records or admin privileges. End-to-end tests are still needed.
- PHPMailer is included as a third-party dependency from the original project; review its license and update it before production use.

## Roadmap

- [ ] Consistent authentication and authorization across all endpoints
- [ ] CSRF protection, input validation and error handling
- [ ] Responsive dashboard redesign and accessible UI
- [ ] Automated tests and reproducible demo data
- [ ] Composer-managed dependencies

## Attribution

Original internship project and portfolio adaptation by Hatice Selenay Güleç. Third-party libraries retain their own licenses.
