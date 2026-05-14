# 🐇 TGRabbitry

A full-stack rabbit farm management and e-commerce web application built with **Laravel 10**, **Vue 3**, **Inertia.js**, and **Tailwind CSS**.

---

## ✨ Features

- 🛒 Online shop with product categories (Rabbits, Food, Accessories, Bundle Packages)
- 📦 Order management with status tracking and invoicing
- 🐰 Rabbit management — breeds, breeding records, weight tracker, illness history
- 💬 Real-time messaging (admin ↔ customer) via Pusher
- 📝 Blog with comments
- 💰 Budget & expense tracking with monthly reports
- ✅ Task / rabbit care scheduler
- 👤 Customer profile with order history and ratings
- 🔐 Admin dashboard with full management controls

---

## 🧰 Requirements

Make sure you have the following installed before running the project:

| Tool | Version |
|------|---------|
| PHP | ^8.1 |
| Composer | Latest |
| Node.js | ^18 or ^20 |
| npm | Latest |
| MySQL | 5.7+ or 8.0+ |
| XAMPP / Laragon / any local server | — |

---

## 🚀 Installation & Setup

### 1. Clone the repository

```bash
git clone https://github.com/melwindaveabe/TGRabbitry.git
cd TGRabbitry
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install Node dependencies

```bash
npm install
```

### 4. Set up environment file

```bash
cp .env.example .env
```

Then open `.env` and update these values:

```env
APP_NAME=TGRabbitry
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tgrabbitry
DB_USERNAME=root
DB_PASSWORD=

PUSHER_APP_ID=your_pusher_app_id
PUSHER_APP_KEY=your_pusher_app_key
PUSHER_APP_SECRET=your_pusher_app_secret
PUSHER_APP_CLUSTER=ap1

BROADCAST_DRIVER=pusher
```

> 💡 For Pusher, create a free account at [pusher.com](https://pusher.com) and create a new app to get your credentials.

### 5. Generate application key

```bash
php artisan key:generate
```

### 6. Create the database

Create a MySQL database named `tgrabbitry` (or whatever you set in `.env`):

```sql
CREATE DATABASE tgrabbitry;
```

### 7. Run migrations

```bash
php artisan migrate
```

### 8. Create storage symlink

```bash
php artisan storage:link
```

### 9. Build frontend assets

```bash
npm run build
```

> For development with hot reload, run instead:
> ```bash
> npm run dev
> ```

### 10. Start the development server

```bash
php artisan serve
```

Visit **http://localhost:8000** in your browser.

---

## 🗂️ Project Structure

```
TGRabbitry/
├── app/
│   ├── Http/Controllers/     # All controllers
│   ├── Models/               # Eloquent models
│   └── Services/             # Helper services
├── database/
│   └── migrations/           # Database schema
├── resources/
│   └── js/
│       ├── Components/       # Reusable Vue components
│       ├── Layouts/          # AuthLayout, GuestLayout
│       └── Pages/            # Inertia page components
├── routes/
│   └── web.php               # All web routes
└── public/
    └── images/               # Uploaded & static images
```

---

## 🔑 Default Admin Access

After migrating, you can create an admin user manually in the database by setting `admin = 1` on a user record, or seed one:

```bash
php artisan db:seed
```

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 10 |
| Frontend | Vue 3 + Inertia.js |
| Styling | Tailwind CSS + Element Plus |
| Charts | Chart.js + vue-chartjs |
| Real-time | Pusher |
| Database | MySQL |
| Build tool | Vite |

---

## 📸 Screenshots

> Login, Dashboard, Shop, Rabbit Management, and more — all with a modern green-themed UI.

---

## 📄 License

This project is open-source and available under the [MIT License](LICENSE).
