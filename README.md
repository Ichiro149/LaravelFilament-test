<div align="center">

# 🛒 ShopLy

### Modern E-Commerce Platform

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-3.x-FDAE4B?style=for-the-badge&logo=laravel&logoColor=white)](https://filamentphp.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white)](https://alpinejs.dev)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-3.x-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![Vite](https://img.shields.io/badge/Vite-5.x-646CFF?style=for-the-badge&logo=vite&logoColor=white)](https://vitejs.dev)

*A full-featured e-commerce solution built with Laravel & Filament*

</div>

---

## ✨ Features

<table>
<tr>
<td width="50%">

### 🛍️ Storefront
- **Product Catalog** with categories & filters
- **Product Variants** (size, color, etc.)
- **Wishlist** & product comparison
- **Shopping Cart** with coupons
- **Customer Reviews** & ratings
- **Recently Viewed** products
- **Dark/Light Theme** support

</td>
<td width="50%">

### 🎛️ Admin Panel
- **Filament Dashboard** for management
- **Order Management** with status tracking
- **Product Import** via Excel/CSV
- **Customer Management**
- **Coupon System**
- **Analytics & Reports**
- **Multi-seller Support**

</td>
</tr>
</table>

### 🔔 Additional Features

| Feature | Description |
|---------|-------------|
| 📧 **Notifications** | Email & in-app notifications for orders and tickets |
| 🎫 **Support Tickets** | Built-in customer support system |
| 📦 **Order Tracking** | Real-time order status updates |
| 💳 **Refund System** | Handle refund requests with status history |
| 📊 **Activity Log** | Track user actions across the platform |
| 🌐 **Multi-language** | i18n support with translation files |

---

## 🚀 Quick Start

### Prerequisites

- PHP 8.2+
- Composer 2.x
- Node.js 18+ & npm
- MySQL 8.0+ / PostgreSQL 14+

### Installation

```bash
# Clone the repository
git clone https://github.com/Vlad8733/LaravelFilament-test.git
cd Filament-test

# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate --seed

# Build assets
npm run build

# Create storage link
php artisan storage:link

# Start the development server
php artisan serve
```

### Development Mode

```bash
# Run Vite dev server with HMR
npm run dev

# In another terminal
php artisan serve
```

---

## 📁 Project Structure

```
├── app/
│   ├── Filament/          # Admin panel resources
│   │   ├── Resources/     # CRUD resources
│   │   └── Seller/        # Seller panel
│   ├── Http/
│   │   ├── Controllers/   # Web controllers
│   │   ├── Livewire/      # Livewire components
│   │   └── Middleware/    # Custom middleware
│   ├── Models/            # Eloquent models
│   ├── Notifications/     # Email & app notifications
│   ├── Observers/         # Model observers
│   └── Policies/          # Authorization policies
├── resources/
│   ├── css/               # Stylesheets (modular)
│   ├── js/                # JavaScript (Alpine components)
│   ├── lang/              # Translation files
│   └── views/             # Blade templates
├── routes/
│   └── web.php            # Web routes
└── database/
    ├── migrations/        # Database migrations
    └── seeders/           # Data seeders
```

---

## 🛠️ Tech Stack

<div align="center">

| Backend | Frontend | Admin | Build |
|:-------:|:--------:|:-----:|:-----:|
| ![Laravel](https://img.shields.io/badge/-Laravel-FF2D20?style=flat-square&logo=laravel&logoColor=white) | ![Alpine.js](https://img.shields.io/badge/-Alpine.js-8BC0D0?style=flat-square&logo=alpine.js&logoColor=white) | ![Filament](https://img.shields.io/badge/-Filament-FDAE4B?style=flat-square&logo=laravel&logoColor=white) | ![Vite](https://img.shields.io/badge/-Vite-646CFF?style=flat-square&logo=vite&logoColor=white) |
| ![PHP](https://img.shields.io/badge/-PHP%208.2-777BB4?style=flat-square&logo=php&logoColor=white) | ![Tailwind](https://img.shields.io/badge/-Tailwind-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white) | ![Livewire](https://img.shields.io/badge/-Livewire-FB70A9?style=flat-square&logo=livewire&logoColor=white) | ![npm](https://img.shields.io/badge/-npm-CB3837?style=flat-square&logo=npm&logoColor=white) |

</div>

---

## 🔧 Configuration

### Environment Variables

```env
# Application
APP_NAME=ShopLy
APP_ENV=local
APP_DEBUG=true

# Database
DB_CONNECTION=mysql
DB_DATABASE=shoply
DB_USERNAME=root
DB_PASSWORD=

# Mail (for notifications)
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025

# Queue (for background jobs)
QUEUE_CONNECTION=database
```

### Queue Worker

For processing background jobs (product imports, notifications):

```bash
php artisan queue:work
```

---

## 📸 Screenshots

<div align="center">
<i>Screenshots coming soon...</i>
</div>

---

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage
```

---

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

<div align="center">

**Built with ❤️ using Laravel & Filament**
</div>
