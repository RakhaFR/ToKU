# Content for the README.md file
readme_content = """# 🛍️ E-Commerce Web Application (Cart & Mass Checkout System)

A lightweight, robust, and native PHP-based e-commerce platform featuring dynamic single-product purchases ("Beli Sekarang") and an advanced session-based multiple-product shopping cart system ("+ Keranjang") with consolidated mass checkout tracking.

## 🚀 Features

* **User Authentication & Session Security:** Complete login protection across critical paths (`checkout.php`, `checkout_massal.php`, `keranjang_detail.php`, `checkout_finish.php`).
* **Dual-Purchase Workflow Architecture:**
    1.  **Instant Purchase ("Beli Sekarang"):** Direct checkout path processing single items instantly to the transaction node.
    2.  **Session-Based Shopping Cart ("+ Keranjang"):** Multi-item storage using native PHP associative arrays (`$_SESSION['cart']`), avoiding redundant database loads until confirmation.
* **Consolidated Mass Checkout:** Bundles multiple cart items under a single unique cryptographic Invoice Identifier (`INV-YYYYMMDD-[HASH]`) while maintaining atomic relational records.
* **Polymorphic Transaction Ledger (`checkout_finish.php`):** A unified, single-file notification and receipt module that handles dynamic routing for both single-item IDs (`?id=`) and grouped multi-item invoices (`?invoice=`).
* **Responsive UI Components:** Clean, modern interface designed with Bootstrap 5 utility layout engines, custom container tables, and integrated FontAwesome typography accents.

---

## 📂 File Directory Structure