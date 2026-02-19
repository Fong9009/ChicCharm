# ChicCharm - Beauty Services Booking & Payment Platform

---

## About the project

This project was built for **FIT3047 Industry Experience** at Monash University, working with a real client to deliver a production-style salon and booking platform.

ChicCharm is a **B2C-style booking platform** where **customers** browse services and book appointments with stylists and see their bookings, **stylists** can view their schedule and bookings, **guests** can book without creating an account, and **admins** approve users, manage bookings and payments, and configure services and content. The app includes registration with role-based access, session-based authentication, customer and stylist dashboards, PayPal integration with webhooks, PDF invoicing and email delivery, and an admin panel for user, booking, payment, and content management.

*This repository is intended for portfolio review. To run it locally, please follow the setup below.*

---

## Features

- **Landing page** — Services, FAQ, contact form, newsletter signup
- **Role-based access** — Customer, Admin, Stylist, and Guest flows with appropriate dashboards
- **Booking system** — Customer and guest booking; admin creation and editing; service–stylist assignment
- **Payments** — PayPal integration with webhooks; payment history and restrictions
- **Invoicing** — PDF invoice generation (mPDF) and email delivery
- **Content management** — Editable content blocks for the landing page
- **Security** — Authentication, authorization, reCAPTCHA on forms
- **Responsive UI** — Bootstrap 5; mobile-friendly layout

---

## Tech Stack

| Layer    | Technology                                                     |
| -------- | -------------------------------------------------------------- |
| Backend  | **PHP 8.4**, **CakePHP 5** (MVC, Auth, Migrations) |
| Frontend | **Bootstrap 5**, Twig-like templates                     |
| Database | **MySQL** (CakePHP ORM)                                  |
| PDF      | **mPDF**                                                 |
| Payments | **PayPal** (webhooks)                                    |
| Other    | reCAPTCHA, Mobile Detect, Content Blocks plugin                |

---

## Quick Start (Local)

**Requirements:** PHP 8.1+ (with extensions `intl`, `gd`, `pdo_mysql`), Composer, MySQL.

```bash
# Clone the repo
git clone https://github.com/Fong9009/ChicCharm.git
cd ChicCharm

# Install dependencies
composer install

# Configure environment (copy and edit)
cp config/app_local.example.php config/app_local.php
# Edit config/app_local.php: set Datasources.default (MySQL host, database, username, password)

# Run migrations (create tables)
bin/cake migrations migrate

# Optional: seed data (if you have a seed)
# bin/cake migrations seed

# Start the built-in server
bin/cake server -p 8765
```

Then open **http://localhost:8765** in your browser.

---

## Configuration

- **Database** — In `config/app_local.php`, set `Datasources.default` to your MySQL connection (host, database name, username, password).
- **PayPal** — Configure webhook URL and credentials in your app config / env if you need live payments.
- **reCAPTCHA** — Keys are in `config/app.php`; replace with your own for production.
- **Email** — Configure mail transport in `config/app_local.php` for invoices and contact/notification emails.

---

## Project Structure (high level)

```
config/          # App and database configuration
src/
  Controller/    # Bookings, Auth, Customers, Stylists, Services, Payments, etc.
  Model/         # Entities and tables (Booking, Customer, Service, Payment, …)
  Mailer/        # Invoice and newsletter mailers
templates/       # View templates
webroot/         # Public assets (CSS, JS, images)
```

---

## Run with Docker (e.g. Railway)

The repo includes a **Dockerfile** that uses PHP 8.4, Apache, and the required extensions. Use it on any Docker-based host (e.g. Railway); the app listens on the `PORT` environment variable.
