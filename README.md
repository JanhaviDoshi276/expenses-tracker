# 💸 Expense Tracker Enhanced — CodeIgniter 3

A full-featured MVC expense tracking app with **multi-user support**, **role-based access**, **currency conversion**, **exchange rates**, and complete **audit trail**.

---

## 📁 Project Structure (application files only)

This ZIP contains only the `application/` layer. You must download CodeIgniter 3 separately.

```
expense-tracker/
├── application/
│   ├── config/           ← autoload, config, database, routes
│   ├── controllers/      ← Auth, Dashboard, Expenses, Categories,
│   │                        Users, ExchangeRates, Export, Profile
│   ├── core/             ← MY_Controller (auth guard, roles, AJAX helpers)
│   ├── models/           ← User, Category, Expense, ExchangeRate models
│   └── views/
│       ├── auth/         ← login.php
│       ├── dashboard/    ← index.php (stats, chart, currency selector)
│       ├── expenses/     ← index.php (transaction history)
│       ├── categories/   ← index.php (CRUD)
│       ├── users/        ← index.php (user management)
│       ├── exports/      ← index.php, pdf_print.php
│       ├── profile/      ← index.php
│       └── layouts/      ← main.php (sidebar + global modals)
├── sql/
│   ├── enhanced_schema.sql   ← Fresh install (MySQL 5.7+)
│   └── migrate_from_v2.sql   ← Upgrade from v2
└── .htaccess
```

---

## ⚙️ Setup — Fresh Install

### 1. Requirements
- PHP 7.4+ (PHP 8.x recommended)  
- MySQL 5.7+ or MariaDB 10.3+  
- Apache with `mod_rewrite` enabled  
- XAMPP / Laragon / WAMP / LAMP  

### 2. Download CodeIgniter 3
```
https://codeigniter.com/download
```
Extract to your web root: e.g. `C:/xampp/htdocs/expense-tracker/`

### 3. Merge project files
Copy everything from this ZIP **into** the CI3 folder (overwrite when asked):
```
application/ → application/
assets/       → assets/
.htaccess     → (project root)
```

### 4. Create the database
Run in phpMyAdmin or MySQL CLI:
```sql
SOURCE /path/to/sql/enhanced_schema.sql;
```

### 5. Configure credentials
**`application/config/database.php`**
```php
'hostname' => 'localhost',
'username' => 'root',
'password' => '',
'database' => 'expense_tracker',
```

**`application/config/config.php`**
```php
$config['base_url'] = 'http://localhost/expense-tracker/';
```

### 6. Enable mod_rewrite
Ensure `.htaccess` is in project root and `AllowOverride All` is set.

### 7. Open in browser
```
http://localhost/expense-tracker/
```

**Default Superadmin Login:**
- Email: `dev1200@yopmail.com`  
- Password: `123456`

---

## ⬆️ Upgrading from v2

Run the migration script instead of fresh schema:
```sql
SOURCE /path/to/sql/migrate_from_v2.sql;
```
This safely adds all new columns (MySQL 5.7 compatible via stored procedures), creates new tables, and seeds the superadmin without losing existing data.

---

## ✨ Features

| Module | Details |
|---|---|
| **Login** | Email + password, bcrypt hashed, account status check |
| **Dashboard** | Month/Year/All-time totals, monthly bar chart, category breakdown. All with live currency conversion |
| **Currency Selector** | Dropdown on dashboard to view all metrics in INR/USD/EUR/GBP/AED/JPY/CAD/AUD |
| **Add Expense** | Global "Add Expense" button in topbar — opens modal with amount, currency, category, date, note |
| **Transaction History** | Full list with audit: added by whom & when. Edit + delete via AJAX modals |
| **Categories** | Admin CRUD — edit propagates to all expenses. Delete moves expenses to "Unspecified" |
| **User Management** | Create/edit/delete users, assign roles (admin/user), set category access per user |
| **Superadmin** | `dev1200@yopmail.com` — permanent, cannot be deleted, full access |
| **Exchange Rates** | Auto-fetch live rates OR manual override per currency per date |
| **Export** | CSV (spreadsheet-ready) and PDF (print-ready) with currency conversion |
| **Profile** | Name, email, preferred currency, avatar upload, change password |

---

## 🔐 Security
- All passwords stored as **bcrypt** hashes (`password_hash` / `password_verify`)
- **CSRF protection** on all forms and AJAX requests
- **Role-based access control**: superadmin > admin > user
- **Category-level data isolation** per user
- `htmlspecialchars()` on all output
- Session stored in database table `ci_sessions`
- All state-changing calls via **AJAX POST** with CSRF token

---

## 🔑 Roles

| Role | Access |
|---|---|
| **superadmin** | All modules, all categories, cannot be deleted/modified |
| **admin** | All categories, can manage users and categories |
| **user** | Only assigned categories, no admin access |

---

## 📦 Tech Stack
- **Backend:** CodeIgniter 3 (PHP 7.4+)  
- **Database:** MySQL 5.7+  
- **Frontend:** Bootstrap 5.3, Font Awesome 6.5, Chart.js 4  
- **Fonts:** Google Fonts — Inter  
