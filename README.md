# ☕ Cafeteria Management System

> **Official Task Division**
> Stack: `PHP` • `Bootstrap 5` • `MySQL` — Equally divided among 5 team members

---

## 📋 Project Overview

Cafeteria is a custom PHP MVC web application for managing a cafeteria system. It supports **admin** and **user** roles, product and category management, order placement, and delivery tracking — built entirely without any external framework.

---

## 🛠 Tech Stack

| Layer      | Technology                          |
|------------|-------------------------------------|
| Backend    | PHP 8.2 — Custom MVC                |
| Database   | MySQL via PDO                       |
| Frontend   | Bootstrap 5.3 + Bootstrap Icons     |
| Server     | Apache / PHP built-in server        |
| Autoload   | Composer                            |

---

## ⚙️ Prerequisites

Make sure the following are installed before running the project:

- **PHP 8.0+** — [php.net](https://www.php.net)
- **Composer** — [getcomposer.org](https://getcomposer.org)
- **XAMPP** (or any Apache + MySQL stack) — [apachefriends.org](https://www.apachefriends.org)
- A modern web browser

---

## 🚀 Installation Steps

### Step 1 — Clone or copy the project

Place the project inside your XAMPP `htdocs` folder:

```powershell
cd C:\xampp\htdocs
git clone https://github.com/AhmedMohO/cafeteria-project.git
cd cafeteria-project
```

### Step 2 — Install Composer dependencies

```powershell
cd C:\xampp\htdocs\cafeteria-project
composer install
```

### Step 3 — Create the environment file

```powershell
copy .env.example .env
```

Then open `.env` and fill in your database credentials:

```env
DB_HOST=localhost
DB_NAME=cafeteria
DB_USER=root
DB_PASS=
```

### Step 4 — Set up the database

**Option A — One command (recommended):**

```powershell
php seeder.php
```

This single command will automatically:
1. Create the `cafeteria` database if it doesn't exist
2. Run `cafeteria.sql` to build all tables
3. Clear any existing data
4. Seed everything from `sampleData.sql`

**Option B — Manual via phpMyAdmin:**

1. Open **phpMyAdmin**: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Create a new database named: **`cafeteria`**
3. Import **`cafeteria.sql`** — creates the database structure
4. Import **`sampleData.sql`** — inserts sample data and the admin user

### Step 5 — Start the development server

```powershell
cd C:\xampp\htdocs\cafeteria-project
php -S localhost:8888 -t public
```

### Step 6 — Open in your browser

```
http://localhost:8888/login
```

---

## 🔐 Login Credentials

### Admin Account

| Field    | Value                   |
|----------|-------------------------|
| Email    | `admin@cafeteria.com`   |
| Password | `123456`                |
| Role     | `admin`                 |

### User Account

| Field    | Value                   |
|----------|-------------------------|
| Email    | `alice@cafeteria.com`   |
| Password | `password123`           |
| Role     | `user`                  |

---

## 📁 Project Structure

```
cafeteria-project/
├── app/
│   ├── Controllers/
│   │   ├── Admin/           — Admin-side controllers
│   │   └── User/            — User-side controllers
│   ├── Middlewares/         — Auth & role middlewares
│   └── Models/              — Database models (PDO)
├── core/                    — Custom MVC framework
│   ├── Router.php
│   ├── Controller.php
│   ├── QueryBuilder.php
│   ├── Database.php
│   └── Auth.php
├── public/                  — Web root (index.php + /uploads)
├── routes/
│   └── web.php              — All application routes
├── views/                   — PHP view templates
│   ├── admin/               — Admin pages
│   ├── user/                — User pages
│   ├── auth/                — Login page
│   └── layouts/             — Shared head, navbar includes
├── cafeteria.sql            — Database schema
├── sampleData.sql           — Sample data & seeded users
├── seeder.php               — CLI seeder script
├── composer.json            — Autoload config
└── .env.example             — Environment file template
```

---

## 👥 Team Member Summary

| Member | Name           | Responsibility                                                                 |
|--------|----------------|--------------------------------------------------------------------------------|
| M1     | Ahmed Wael     | **Infrastructure & Auth** — Login, logout, session management, DB config, shared includes, dashboard analytics |
| M2     | Khaled         | **Catalog Management** — Products list, add/edit/delete products, category management |
| M3     | Ahmed Mohammed Mostafa  | **User Management** — Users list, add/edit/delete users, Project image upload, seeder  |
| M4     | Reda           | **Client-Side Orders** — Home/browse page, place order, My Orders, cancel order |
| M5     | Amira          | **Admin Operations** — Full orders list, manual order entry, checks/reports |

---

<div align="center">

**Cafeteria Management System** • 5-Member Task Division • PHP + Bootstrap 5 + MySQL

</div>
