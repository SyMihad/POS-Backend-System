# Mini POS API (Multi-Tenant)

A multi-tenant POS backend built with **Laravel 12**, implementing authentication, role-based access control, inventory-safe order processing, and reporting.

This project was developed as a **technical assignment** and follows best practices such as:

* Policy-based authorization
* Database transactions
* Tenant isolation
* Clean REST APIs

---

## 🚀 Tech Stack

* **Laravel 12**
* **PHP 8.2+**
* **MySQL**
* **Laravel Sanctum** (API authentication)

---

## 🧠 Core Concepts

### Multi-Tenancy

* Each user belongs to a `tenant`
* All business data (products, customers, orders, reports) is **tenant-isolated**
* Implemented using a **global Eloquent scope** (`TenantScope`)

### Roles

| Role  | Capabilities                                       |
| ----- | -------------------------------------------------- |
| Owner | Manage products, staff, customers, orders, reports |
| Staff | Create & manage orders, view customers and reports |

Authorization is enforced using **Laravel Policies** (no controller-level checks).

---

## 🔐 Authentication

Authentication is handled using **Laravel Sanctum**.

### Login

```
POST /api/login
```

### Logout

```
POST /api/logout
```

All protected routes require:

* `Authorization: Bearer <token>`
* `x-tenant-id` header

---

## 📦 Products (Owner Only)

Full CRUD operations are implemented.

### List Products

```
GET /api/products
```

### Create Product

```
POST /api/products
```

### Update Product

```
PUT /api/products/{product_id}
```

---

## 👤 Customers

Full CRUD operations are implemented.

### List Customers

```
GET /api/customers
```

### Create Customer

```
POST /api/customers
```

### Update Customer

```
PUT /api/customers/{customer_id}
```

---

## 🧾 Orders & Inventory

Order lifecycle:

```
pending → paid → cancelled
```

Inventory rules:

* Stock is deducted **only when an order is paid**
* Stock is restored if a **paid order is cancelled**

### Create Order (Pending)

```
POST /api/orders
```

```json
{
  "customer_id": 1,
  "items": [
    { "product_id": 1, "qty": 2 },
    { "product_id": 2, "qty": 1 }
  ]
}
```

---

### Pay Order

```
POST /api/orders/{order_id}/pay
```

* Deducts stock
* Uses database transactions & row locking

---

### Cancel Order

```
POST /api/orders/{order_id}/cancel
```

* Restores stock **only if already paid**

---

### List Orders (With Details)

```
GET /api/orders
```

Includes:

* Customer info
* Order items
* Product details
* Subtotals and totals

---

## 👥 Staff Management (Owner Only)

### List Staff

```
GET /api/staff
```

### Add Staff

```
POST /api/staff
```

```json
{
  "name": "Sales Staff",
  "email": "staff@example.com",
  "password": "password123"
}
```

Staff users belong to the same tenant and can log in normally.

---

## 📊 Reports

All reports consider **only paid orders** and are **tenant-isolated**.

### Daily Sales Report

```
GET /api/reports/daily-sales
```

Returns total sales and total orders for the current day.

---

### Top 5 Selling Products (Date Range Based)

```
GET /api/reports/top-products?from=YYYY-MM-DD&to=YYYY-MM-DD
```

* Calculates top 5 products based on **total quantity sold**
* Uses a **selected date range** (`from` → `to`)
* Considers **only paid orders**

Example:

```
GET /api/reports/top-products?from=2026-01-01&to=2026-01-31
```

---

### Low Stock Products

```
GET /api/reports/low-stock
```

Returns products with stock below a predefined threshold.

---

## 🛡 Authorization Strategy

* No authorization logic inside controllers
* All permissions enforced via **Policies**
* Laravel 12 policy auto-discovery is used (no AuthServiceProvider)

---

## ⚙️ Setup Instructions

```bash
git clone <repository-url>
cd mini-pos
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

Ensure storage permissions are correct:

```bash
chmod -R 775 storage bootstrap/cache
```

---

## 🏁 Final Notes

This project prioritizes **data integrity**, **security**, and **real-world POS workflows**. All required features from the assignment have been implemented with clean, maintainable code.

---

**Author:** Shajaratul Yakin
