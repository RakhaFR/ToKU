# 🛍️ E-Commerce Web Application by Rakha FR

## Cart & Consolidated Checkout System

A native PHP-based E-Commerce web application designed to handle online product transactions with dual-purchase workflows, shopping cart management, mass checkout processing, invoice generation, and integrated admin management.

---

# 📌 Project Overview

This application functions as an online shopping platform where users can browse products, add multiple items into a shopping cart, and complete purchases either instantly or collectively using a unified invoice system.

The system is optimized using lightweight native PHP architecture combined with Bootstrap responsive interfaces and session-based cart handling.

---

# 💻 Technologies Used

| Technology                    | Purpose                                 |
| ----------------------------- | --------------------------------------- |
| PHP Native (Vanilla PHP)      | Core backend programming                |
| MySQL / MariaDB               | Database management                     |
| MySQLi Extension              | Database interaction                    |
| Bootstrap 5                   | Responsive UI framework                 |
| FontAwesome / Bootstrap Icons | Icon system                             |
| PHP Session (`$_SESSION`)     | Temporary cart & login state management |

---

# 📂 Project Directory Structure

```text
├── admin/
│   ├── admin_kategori.php
│   ├── admin_laporan.php
│   ├── admin_media.php
│   ├── admin_produk.php
│   └── admin.php
│
├── assets/
│   ├── bootstrap/
│   ├── css/
│   └── images/
│
├── config/
│   └── koneksi.php
│
├── includes/
│   ├── footer.php
│   ├── header.php
│   └── sidebar_admin.php
│
├── startbootstrap-sb-admin-gh-pages/
│
├── checkout_finish.php
├── checkout_massal.php
├── checkout.php
├── index.php
├── keranjang_detail.php
├── keranjang_proses.php
├── login_proses.php
├── login.php
└── logout.php
```

---

# 📖 Folder & File Descriptions

## 🔐 Authentication System

| File               | Description                    |
| ------------------ | ------------------------------ |
| `login.php`        | User login interface           |
| `login_proses.php` | Login verification processor   |
| `logout.php`       | Session destroy/logout handler |

---

## 🛒 Shopping & Checkout System

| File                   | Description                    |
| ---------------------- | ------------------------------ |
| `index.php`            | Product catalog homepage       |
| `keranjang_proses.php` | Add-to-cart session processor  |
| `keranjang_detail.php` | Shopping cart detail page      |
| `checkout.php`         | Instant checkout form          |
| `checkout_massal.php`  | Bulk checkout form             |
| `checkout_finish.php`  | Dynamic invoice & success page |

---

## ⚙️ Admin Panel

| File                       | Description                       |
| -------------------------- | --------------------------------- |
| `admin/admin.php`          | Main admin dashboard              |
| `admin/admin_produk.php`   | Product CRUD management           |
| `admin/admin_kategori.php` | Product category management       |
| `admin/admin_media.php`    | Media & image management          |
| `admin/admin_laporan.php`  | Transaction reports & sales recap |

---

## 🧩 Shared Components

| File                         | Description                |
| ---------------------------- | -------------------------- |
| `includes/header.php`        | Navigation & global assets |
| `includes/footer.php`        | Closing HTML & JS scripts  |
| `includes/sidebar_admin.php` | Admin sidebar navigation   |

---

# 🚀 Main Features

## 1. 🔒 Authentication & Session Security

* Session-protected routes
* Unauthorized URL access prevention
* Login validation system
* Session-based user management

---

## 2. 🛍️ Dual Purchase Workflow

### ✅ Instant Checkout ("Beli Sekarang")

Allows users to directly purchase a single product without affecting cart contents.

### ✅ Shopping Cart System ("+ Keranjang")

Users can:

* Store multiple products
* Modify quantities
* Remove items dynamically
* Maintain cart state using `$_SESSION['cart']`

---

## 3. 📦 Consolidated Mass Checkout

All cart items are merged into one invoice code format:

```text
INV-YYYYMMDD-[HASH]
```

Benefits:

* Cleaner transaction grouping
* Easier purchase tracking
* Better invoice organization

---

## 4. 🧾 Dynamic Invoice System

`checkout_finish.php` intelligently handles:

### Single Purchase

```text
? id = transaction_id
```

### Mass Checkout

```text
? invoice = invoice_code
```

The page dynamically generates transaction receipts depending on the transaction type.

---

## 5. 📊 Admin Dashboard System

The admin panel provides:

* Product management
* Category management
* Media/image handling
* Transaction reporting
* Sales recap system

---

# 🛠️ Database Architecture

## 1. `produk` Table

Stores all active product inventory data.

| Column       | Description                       |
| ------------ | --------------------------------- |
| `no`         | Product ID (Primary Key)          |
| `namaproduk` | Product name                      |
| `harga`      | Product price                     |
| `image`      | Product image filename            |
| `ket`        | Product description/specification |

---

## 2. `checkoutfinish` Table

Stores completed transaction records.

| Column        | Description            |
| ------------- | ---------------------- |
| `no`          | Transaction ID         |
| `namabarang`  | Purchased product name |
| `pembeli`     | Customer name          |
| `invoice`     | Invoice code           |
| `rekbank`     | Payment method         |
| `harga`       | Unit price             |
| `qty`         | Quantity purchased     |
| `total_harga` | Final total cost       |

---

# 🔄 User Workflow

## 👤 Customer Side

1. User logs in via `login.php`
2. Browse products in `index.php`
3. Choose:

   * **Beli Sekarang**
   * **+ Keranjang**
4. Open cart page (`keranjang_detail.php`)
5. Proceed to:

   * Single checkout
   * Mass checkout
6. Complete payment information
7. Receive transaction invoice

---

## 👨‍💼 Admin Side

1. Access admin dashboard:

```text
localhost/project_name/admin/admin.php
```

2. Manage:

* Products
* Categories
* Media files
* Transaction reports

---

# ⚙️ Installation Guide

## 1. Place Project Inside Server Directory

### XAMPP

```text
C:/xampp/htdocs/
```

### Linux Apache

```text
/var/www/html/
```

---

## 2. Configure Database

Edit:

```text
config/koneksi.php
```

Example:

```php
<?php

$koneksi = mysqli_connect(
    "localhost",
    "root",
    "",
    "nama_database"
);

?>
```

---

## 3. Import Database Tables

Use:

* phpMyAdmin
* MySQL Workbench
* Adminer

Create tables based on the schema above.

---

## 4. Run Project

Open browser:

```text
http://localhost/nama_project/index.php
```

---

# 🎨 UI & Design Components

* Bootstrap 5 Responsive Layout
* FontAwesome Icons
* Custom CSS Styling
* Session-Based Dynamic Navigation
* Admin Dashboard Template Integration

---

# 📌 System Highlights

✅ Native PHP Architecture
✅ Session-Based Shopping Cart
✅ Mass Checkout System
✅ Dynamic Invoice Generator
✅ Responsive Bootstrap UI
✅ Admin Dashboard Integration
✅ Lightweight & Easy Deployment

---

# 👨‍💻 Developer

**Rakha Fadilah Riyadi**
Front-End & Web Developer
