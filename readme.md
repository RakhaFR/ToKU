# 🛍️ E-Commerce Web Application (Cart & Mass Checkout System)

A lightweight, robust, and native PHP-based e-commerce platform featuring dynamic single-product purchases ("Beli Sekarang") and an advanced session-based multiple-product shopping cart system ("+ Keranjang") with consolidated mass checkout tracking.

---

## 🚀 Features

* **User Authentication & Session Security**
  Complete login protection across critical paths:

  * `checkout.php`
  * `checkout_massal.php`
  * `keranjang_detail.php`
  * `checkout_finish.php`

* **Dual-Purchase Workflow Architecture**

  ### 1. Instant Purchase ("Beli Sekarang")

  Direct checkout path processing single items instantly to the transaction node.

  ### 2. Session-Based Shopping Cart ("+ Keranjang")

  Multi-item storage using native PHP associative arrays (`$_SESSION['cart']`) without redundant database loading until checkout confirmation.

* **Consolidated Mass Checkout**

  Bundles multiple cart items under a single unique cryptographic Invoice Identifier:

  ```text
  INV-YYYYMMDD-[HASH]
  ```

* **Polymorphic Transaction Ledger (`checkout_finish.php`)**

  Unified dynamic receipt system supporting:

  * Single item transaction (`?id=`)
  * Grouped invoice transaction (`?invoice=`)

* **Responsive UI Components**

  Built using:

  * Bootstrap 5
  * FontAwesome
  * Custom responsive table layouts

---

# 📂 File Directory Structure

```text
├── config/
│   └── koneksi.php
│
├── includes/
│   ├── header.php
│   └── footer.php
│
├── index.php
├── keranjang_proses.php
├── keranjang_detail.php
├── checkout_massal.php
└── checkout_finish.php
```

### Description

| File                   | Function                                  |
| ---------------------- | ----------------------------------------- |
| `config/koneksi.php`   | Database configuration and initialization |
| `includes/header.php`  | Global navigation and assets              |
| `includes/footer.php`  | Closing HTML and script attachments       |
| `index.php`            | Product catalog display                   |
| `keranjang_proses.php` | Add-to-cart processing                    |
| `keranjang_detail.php` | Cart dashboard and totals                 |
| `checkout_massal.php`  | Bulk checkout form                        |
| `checkout_finish.php`  | Invoice & success page                    |

---

# 🛠️ Data Architecture (Database Design)

## 1. `produk` Table

Stores active product catalog data.

| Column Name  | Data Type    | Attributes                  | Description                       |
| ------------ | ------------ | --------------------------- | --------------------------------- |
| `no`         | INT          | PRIMARY KEY, AUTO_INCREMENT | Unique product ID                 |
| `namaproduk` | VARCHAR(100) | NOT NULL                    | Product name                      |
| `harga`      | INT          | NOT NULL                    | Product price                     |
| `image`      | VARCHAR(255) | NOT NULL                    | Product image filename            |
| `ket`        | TEXT         | NULL                        | Product description/specification |

---

## 2. `checkoutfinish` Table

Stores completed transaction records.

| Column Name   | Data Type    | Attributes                  | Description            |
| ------------- | ------------ | --------------------------- | ---------------------- |
| `no`          | INT          | PRIMARY KEY, AUTO_INCREMENT | Transaction ID         |
| `namabarang`  | VARCHAR(100) | NOT NULL                    | Purchased product name |
| `pembeli`     | VARCHAR(100) | NOT NULL                    | Customer name          |
| `invoice`     | VARCHAR(50)  | NOT NULL                    | Invoice grouping code  |
| `rekbank`     | VARCHAR(100) | NOT NULL                    | Payment information    |
| `harga`       | INT          | NOT NULL                    | Unit price             |
| `qty`         | INT          | NOT NULL                    | Product quantity       |
| `total_harga` | INT          | NOT NULL                    | Final total price      |

---

# ⚙️ Installation & Deployment

## 1. Clone or Download Project

Place the project inside your server root directory.

### XAMPP Example

```text
C:/xampp/htdocs/
```

### Linux Apache Example

```text
/var/www/html/
```

---

## 2. Configure Database Connection

Edit:

```text
config/koneksi.php
```

Then update the credentials:

```php
<?php

$koneksi = mysqli_connect(
    "localhost",
    "your_username",
    "your_password",
    "your_db_name"
);

?>
```

---

## 3. Import Database Tables

Use:

* phpMyAdmin
* MySQL Workbench
* Adminer
* or MySQL CLI

Create tables based on the schema structure above.

---

## 4. Run the Application

Open browser:

```text
http://localhost/your_project_folder/index.php
```

---

## 5. Authentication

Login authentication/session must be active before accessing protected routes.

---

# 🧩 Technology Stack

* PHP Native
* MySQL
* Bootstrap 5
* FontAwesome
* HTML5
* CSS3
* JavaScript

---

# 📸 System Highlights

✅ Session-based cart system
✅ Dynamic invoice generation
✅ Mass checkout support
✅ Responsive interface
✅ Lightweight architecture
✅ Clean procedural PHP structure

---

# 👨‍💻 Developer

Developed by Rakha Fadilah Riyadi
Front-End & PHP Web Developer
