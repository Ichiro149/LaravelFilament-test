<![CDATA[<div align="center">

# 🛒 ShopLy

### Modern E-Commerce Platform

<br>

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-3.2-FDAE4B?style=for-the-badge&logo=laravel&logoColor=white)](https://filamentphp.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Tests](https://img.shields.io/badge/Tests-84%20Passed-22C55E?style=for-the-badge&logo=checkmarx&logoColor=white)](#-testing)

<br>

[![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=flat-square&logo=alpine.js&logoColor=white)](https://alpinejs.dev)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-3.x-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![Vite](https://img.shields.io/badge/Vite-7.x-646CFF?style=flat-square&logo=vite&logoColor=white)](https://vitejs.dev)
[![Livewire](https://img.shields.io/badge/Livewire-3.x-FB70A9?style=flat-square&logo=livewire&logoColor=white)](https://livewire.laravel.com)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://mysql.com)
[![Redis](https://img.shields.io/badge/Redis-7.x-DC382D?style=flat-square&logo=redis&logoColor=white)](https://redis.io)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=flat-square&logo=docker&logoColor=white)](#-docker-installation)

<br>

**Full-featured marketplace with seller companies, admin panel & customer storefront**

<br>

[📖 Features](#-features) •
[🚀 Installation](#-installation) •
[💻 Development](#-development) •
[📁 Structure](#-project-structure) •
[🔌 API](#-api-reference) •
[🧪 Tests](#-testing)

</div>

---

## 📋 Table of Contents

- [✨ Features](#-features)
  - [🛍️ Customer Storefront](#️-customer-storefront)
  - [🏢 Company System](#-company-system)
  - [🎛️ Admin Panel](#️-admin-panel)
  - [👨‍💼 Seller Panel](#-seller-panel)
  - [🔔 System Features](#-system-features)
- [🛠️ Tech Stack](#️-tech-stack)
- [🚀 Installation](#-installation)
  - [🐳 Docker](#-docker-installation)
  - [💻 Local Setup](#-local-setup)
- [💻 Development](#-development)
- [📁 Project Structure](#-project-structure)
- [🔌 API Reference](#-api-reference)
- [🧪 Testing](#-testing)
- [⚙️ Configuration](#️-configuration)

---

## ✨ Features

### 🛍️ Customer Storefront

<table>
<tr>
<td>

**🔍 Product Discovery**
- Product catalog with advanced filters
- Category navigation with hierarchy
- Full-text search (products & companies)
- Recently viewed products
- Related products suggestions

</td>
<td>

**🛒 Shopping Experience**
- Shopping cart with quantity management
- Wishlist for saving favorites
- Product comparison (side-by-side)
- Discount coupons support
- Guest & authenticated checkout

</td>
</tr>
<tr>
<td>

**⭐ Customer Engagement**
- Product reviews & star ratings
- Follow your favorite companies
- Order tracking by number
- Support tickets with attachments
- Email notifications

</td>
<td>

**🎨 User Experience**
- Dark / Light theme toggle
- Multi-language (EN, RU, LV)
- Fully responsive design
- Fast page loads with Vite
- Real-time updates with Livewire

</td>
</tr>
</table>

---

### 🏢 Company System

> 🆕 **New!** Role-based seller system with company profiles

| Feature | Description |
|:--------|:------------|
| 🏪 **Company Profiles** | Each seller creates a company with logo, banner, description, and contact info |
| 📍 **Public Storefront** | Customers visit `/companies/{slug}` to see company profile and products |
| ❤️ **Follow System** | Users can follow their favorite sellers to stay updated |
| ✅ **Verification Badge** | Admins can verify trusted companies with a badge |
| 📦 **Product Ownership** | All products belong to a specific company, not just user |

**Routes:**
```
/companies              → Browse all companies with search & filters
/companies/{slug}       → Company profile page with products
/companies/{id}/follow  → Follow/unfollow a company
/seller                 → Seller panel to manage your company
```

---

### 🎛️ Admin Panel

> Access at `/admin` — Full control over the platform

<table>
<tr>
<th width="25%">📦 Catalog</th>
<th width="25%">📋 Orders</th>
<th width="25%">👥 Users</th>
<th width="25%">🔧 System</th>
</tr>
<tr>
<td valign="top">

- Products CRUD
- Product variants
- Product images
- Categories
- CSV import/export
- Company assignment

</td>
<td valign="top">

- Order management
- Status transitions
- Order history
- Refund requests
- Invoice generation
- Status notifications

</td>
<td valign="top">

- Customer accounts
- Role management
- Activity logs
- Support tickets
- Ticket replies
- User search

</td>
<td valign="top">

- Company verification
- Company moderation
- Coupon management
- Review moderation
- Import jobs monitor
- Failed imports

</td>
</tr>
</table>

---

### 👨‍💼 Seller Panel

> Access at `/seller` — Dedicated dashboard for sellers

| Feature | Description |
|:--------|:------------|
| 🏪 **Company Profile** | Create and edit your company with logo, banner, description, contacts |
| 📦 **Product Management** | Full CRUD for products with variants, images, categories |
| 📊 **Dashboard** | Overview of your company statistics and recent activity |
| 🔗 **Public Link** | Share your company page: `/companies/your-company-slug` |

---

### 🔔 System Features

| Feature | Description |
|:--------|:------------|
| 📧 **Notifications** | In-app and email notifications for orders, tickets, status changes |
| 🎫 **Support Tickets** | Built-in ticketing system with file attachments and replies |
| 📝 **Activity Log** | Track user actions across the platform |
| 🌍 **Multi-language** | English, Russian, and Latvian (en, ru, lv) |
| 📄 **PDF Invoices** | Generate downloadable invoices using DomPDF |
| 🌙 **Dark/Light Theme** | User preference for theme switching |
| 🔐 **Two-Factor Auth** | Optional 2FA for enhanced security |

---

## 🛠️ Tech Stack

### Backend

| Technology | Version | Purpose |
|:-----------|:-------:|:--------|
| **PHP** | 8.2+ | Runtime environment |
| **Laravel** | 12.x | Web framework |
| **Filament** | 3.2 | Admin & seller panels |
| **Livewire** | 3.x | Reactive components |
| **DomPDF** | 3.1 | PDF invoice generation |

### Frontend

| Technology | Version | Purpose |
|:-----------|:-------:|:--------|
| **Alpine.js** | 3.x | JavaScript framework |
| **Tailwind CSS** | 3.x | Utility-first CSS |
| **Vite** | 7.x | Build tool with HMR |

### Database & Cache

| Technology | Version | Purpose |
|:-----------|:-------:|:--------|
| **MySQL** | 8.0+ | Primary database (recommended) |
| **PostgreSQL** | 14+ | Alternative database |
| **SQLite** | 3.x | Development & testing |
| **Redis** | 7.x | Cache & sessions (optional) |

---

## 🚀 Installation

### 🐳 Docker Installation

> **Recommended** — The fastest way to get started

```bash
# Clone the repository
git clone <repository-url>
cd filament-test

# Copy Docker environment
cp .env.docker .env

# Build and start everything
make init
```

**🌐 Access:** http://localhost:8080

<details>
<summary><b>📋 Available Make Commands</b></summary>

| Command | Description |
|:--------|:------------|
| `make up` | Start all containers |
| `make down` | Stop all containers |
| `make shell` | Open shell in app container |
| `make logs` | View container logs |
| `make test` | Run test suite |
| `make fresh` | Fresh migration with seeders |
| `make mysql` | Open MySQL CLI |
| `make redis` | Open Redis CLI |
| `make pint` | Run code style fixer |

</details>

<details>
<summary><b>🐋 Docker Services</b></summary>

| Service | Port | Description |
|:--------|:----:|:------------|
| **nginx** | 8080 | Web server |
| **mysql** | 3306 | Database |
| **redis** | 6379 | Cache & sessions |
| **queue** | — | Background job worker |
| **mailpit** | 8025 | Email testing UI |

</details>

---

### 💻 Local Setup

#### Prerequisites

- PHP 8.2 or higher
- Composer 2.x
- Node.js 18+ with npm
- MySQL 8.0+ / PostgreSQL 14+ / SQLite

#### Quick Setup

```bash
# Clone the repository
git clone <repository-url>
cd filament-test

# Run automated setup
composer setup
```

#### Manual Installation

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Environment configuration
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate --seed

# Build frontend assets
npm run build

# Create storage link
php artisan storage:link

# Start the server
php artisan serve
```

**🌐 Access:** http://localhost:8000

---

## 💻 Development

### Start Development Server

```bash
# Start all services concurrently
composer dev
```

This starts:
| Service | Description |
|:--------|:------------|
| 🌐 **Laravel** | Development server at `localhost:8000` |
| ⚡ **Vite** | Hot Module Replacement for assets |
| 📋 **Queue** | Background job processing |
| 📝 **Pail** | Real-time log tailing |

### Individual Commands

```bash
php artisan serve        # Laravel development server
npm run dev              # Vite with HMR
php artisan queue:work   # Queue worker (for imports, notifications)
php artisan pail         # Real-time logs
```

### Build for Production

```bash
npm run build
```

### Code Style

```bash
# Fix code style with Laravel Pint
vendor/bin/pint
```

---

## 📁 Project Structure

```
📦 filament-test/
│
├── 📂 app/
│   ├── 📂 Filament/
│   │   ├── 📂 Resources/              # 👑 Admin panel resources
│   │   │   ├── ProductResource.php    #    Products (moderation, company assignment)
│   │   │   ├── CompanyResource.php    #    Companies (verification, moderation)
│   │   │   ├── OrderResource.php      #    Orders (status, history)
│   │   │   ├── UserResource.php       #    Users (accounts, roles)
│   │   │   ├── CouponResource.php     #    Discount coupons
│   │   │   ├── TicketResource.php     #    Support tickets
│   │   │   └── ...
│   │   │
│   │   └── 📂 Seller/                 # 🏪 Seller panel
│   │       └── 📂 Resources/
│   │           ├── CompanyResource.php    # Own company management
│   │           └── ProductResource.php    # Company products
│   │
│   ├── 📂 Http/
│   │   ├── 📂 Controllers/            # 🌐 Web controllers
│   │   │   ├── CompanyController.php  #    Company pages & follow
│   │   │   ├── ProductController.php  #    Product catalog
│   │   │   ├── CartController.php     #    Shopping cart
│   │   │   ├── WishlistController.php #    Wishlist
│   │   │   ├── CheckoutController.php #    Checkout & orders
│   │   │   ├── TicketController.php   #    Support tickets
│   │   │   ├── SearchController.php   #    Global search
│   │   │   └── ...
│   │   │
│   │   ├── 📂 Livewire/               # ⚡ Livewire components
│   │   └── 📂 Middleware/             # 🔒 Custom middleware
│   │
│   ├── 📂 Models/                     # 📊 Eloquent models (20+)
│   │   ├── User.php
│   │   ├── Company.php                #    Seller companies
│   │   ├── CompanyFollow.php          #    Follow relationships
│   │   ├── Product.php                #    Products (→ Company)
│   │   ├── ProductVariant.php
│   │   ├── ProductImage.php
│   │   ├── Category.php
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   ├── OrderStatus.php
│   │   ├── CartItem.php
│   │   ├── WishlistItem.php
│   │   ├── Coupon.php
│   │   ├── Review.php
│   │   ├── Ticket.php
│   │   ├── TicketMessage.php
│   │   └── ...
│   │
│   ├── 📂 Notifications/              # 📧 Email & in-app notifications
│   ├── 📂 Observers/                  # 👀 Model event observers
│   ├── 📂 Policies/                   # 🔐 Authorization policies
│   └── 📂 Jobs/                       # ⚙️ Background jobs
│       └── ImportProductsJob.php
│
├── 📂 database/
│   ├── 📂 factories/                  # 🏭 Model factories for testing
│   ├── 📂 migrations/                 # 📋 Database schema
│   └── 📂 seeders/                    # 🌱 Sample data
│
├── 📂 resources/
│   ├── 📂 css/                        # 🎨 Modular stylesheets
│   ├── 📂 js/                         # 📜 Alpine.js components
│   ├── 📂 lang/                       # 🌍 Translations
│   │   ├── 📂 en/                     #    English
│   │   ├── 📂 ru/                     #    Russian
│   │   └── 📂 lv/                     #    Latvian
│   └── 📂 views/                      # 🖼️ Blade templates
│
└── 📂 tests/
    ├── 📂 Feature/                    # 🧪 Feature tests (84 tests)
    │   ├── AuthTest.php
    │   ├── CartTest.php
    │   ├── OrderTest.php
    │   ├── ProductTest.php
    │   ├── WishlistTest.php
    │   ├── CouponTest.php
    │   ├── ReviewTest.php
    │   └── ...
    └── 📂 Unit/                       # 🔬 Unit tests
```

---

## 🔌 API Reference

### 🏢 Companies

| Method | Endpoint | Description |
|:------:|:---------|:------------|
| `GET` | `/companies` | Browse all companies with search & filters |
| `GET` | `/companies/{slug}` | Company profile page with products |
| `POST` | `/companies/{id}/follow` | Follow/unfollow a company (auth required) |

### 📦 Products

| Method | Endpoint | Description |
|:------:|:---------|:------------|
| `GET` | `/products` | Product listing with filters |
| `GET` | `/products/{slug}` | Product detail page |
| `GET` | `/category/{slug}` | Products by category |
| `GET` | `/search` | Global search (products & companies) |
| `GET` | `/recently-viewed` | Recently viewed products |

### 🛒 Cart

| Method | Endpoint | Description |
|:------:|:---------|:------------|
| `GET` | `/cart` | View shopping cart |
| `POST` | `/cart/add/{productId}` | Add product to cart |
| `PATCH` | `/cart/update/{itemId}` | Update item quantity |
| `DELETE` | `/cart/remove/{itemId}` | Remove item from cart |
| `GET` | `/cart/count` | Get cart items count (JSON) |
| `POST` | `/cart/coupon/apply` | Apply discount coupon |
| `DELETE` | `/cart/coupon/remove` | Remove applied coupon |

### ❤️ Wishlist

| Method | Endpoint | Description |
|:------:|:---------|:------------|
| `GET` | `/wishlist` | View wishlist |
| `POST` | `/wishlist/add/{productId}` | Add product to wishlist |
| `DELETE` | `/wishlist/remove/{productId}` | Remove from wishlist |
| `GET` | `/wishlist/count` | Get wishlist count (JSON) |

### 📋 Orders

| Method | Endpoint | Description |
|:------:|:---------|:------------|
| `GET` | `/checkout` | Checkout page |
| `POST` | `/checkout` | Place order |
| `GET` | `/track-order` | Order tracking form |
| `POST` | `/track-order` | Search by order number |
| `GET` | `/track-order/{orderNumber}` | View order status & details |
| `GET` | `/orders/{order}/invoice` | Download PDF invoice |

### 🔄 Compare

| Method | Endpoint | Description |
|:------:|:---------|:------------|
| `GET` | `/compare` | View comparison table |
| `POST` | `/compare/add/{productId}` | Add to comparison |
| `DELETE` | `/compare/remove/{productId}` | Remove from comparison |
| `DELETE` | `/compare/clear` | Clear all comparisons |

### ⭐ Reviews

| Method | Endpoint | Description |
|:------:|:---------|:------------|
| `GET` | `/reviews/create/{product}` | Review form |
| `POST` | `/reviews` | Submit review |

### 🎫 Support

| Method | Endpoint | Description |
|:------:|:---------|:------------|
| `GET` | `/support` | List user tickets |
| `GET` | `/support/create` | Create ticket form |
| `POST` | `/support` | Submit new ticket |
| `GET` | `/support/{ticket}` | View ticket & messages |
| `POST` | `/support/{ticket}/reply` | Reply to ticket |

### 🌍 Language

| Method | Endpoint | Description |
|:------:|:---------|:------------|
| `GET` | `/language/{locale}` | Switch language (en, ru, lv) |

---

## 🧪 Testing

### Run Tests

```bash
# Run all tests
php artisan test

# Run with verbose output
php artisan test -v

# Run specific test file
php artisan test --filter=CartTest

# Run specific test method
php artisan test --filter=CartTest::test_can_add_product_to_cart

# Using Composer script
composer test
```

### Test Coverage

| Suite | Tests | What's Tested |
|:------|:-----:|:--------------|
| 🔐 **AuthTest** | 14 | Registration, login, logout, profile, password change |
| 🛒 **CartTest** | 12 | Add, update, remove, stock validation, guest cart |
| 📦 **OrderTest** | 9 | Checkout flow, order placement, tracking, validation |
| 🏷️ **ProductTest** | 11 | Listing, details, categories, slug generation |
| ❤️ **WishlistTest** | 9 | Add, remove, user isolation, persistence |
| 🎟️ **CouponTest** | 14 | Validation, calculation, apply/remove, expiry |
| ⭐ **ReviewTest** | 7 | Submit, moderation, rating queries |
| 📥 **ImportTest** | 6 | CSV import, validation, variants, failures |
| **Total** | **84** | — |

---

## ⚙️ Configuration

### Environment Variables

```env
# ═══════════════════════════════════════════
# Application
# ═══════════════════════════════════════════
APP_NAME=ShopLy
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# ═══════════════════════════════════════════
# Database
# ═══════════════════════════════════════════
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=shoply
DB_USERNAME=root
DB_PASSWORD=

# ═══════════════════════════════════════════
# Queue (required for imports & notifications)
# ═══════════════════════════════════════════
QUEUE_CONNECTION=database

# ═══════════════════════════════════════════
# Mail
# ═══════════════════════════════════════════
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_FROM_ADDRESS="shop@example.com"
MAIL_FROM_NAME="${APP_NAME}"

# ═══════════════════════════════════════════
# Session & Cache
# ═══════════════════════════════════════════
SESSION_DRIVER=database
CACHE_STORE=database
# Or use Redis:
# SESSION_DRIVER=redis
# CACHE_STORE=redis
# REDIS_HOST=127.0.0.1
```

### Queue Worker

Background jobs are used for:
- 📥 Bulk product imports
- 📧 Email notifications  
- 🔔 Order status change notifications

```bash
# Start queue worker
php artisan queue:work

# Or use supervisor in production
# Or use the dev script which includes queue
composer dev
```

### Languages

**Supported:** English (en), Russian (ru), Latvian (lv)

**Switch language:**
- Via URL: `/language/en`, `/language/ru`, `/language/lv`
- Stored in session, persists across requests

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

<div align="center">

### Built with ❤️ using

[![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-FDAE4B?style=for-the-badge&logo=laravel&logoColor=white)](https://filamentphp.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white)](https://alpinejs.dev)
[![Tailwind](https://img.shields.io/badge/Tailwind-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)

<br>

**⭐ Star this repo if you find it useful!**

</div>
]]>
