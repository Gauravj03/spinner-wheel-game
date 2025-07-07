# 🎯 Spinning Wheel Game — Laravel 11, Livewire 3, Alpine.js

A fun, interactive spinning wheel game where users can log in, top up their balance, spin to win (or lose!) credits, and track results in real-time — built using **Laravel 11**, **Livewire 3**, and **Alpine.js**.

---

## 📖 User Story

> As a user, I want to play a spinning wheel game where I can log in, top up my balance, and have the game results stored in a database.

---

## 🚀 Features

✅ User authentication (Laravel Breeze with Livewire)  
✅ Balance system with “Top Up” functionality  
✅ Interactive spinning wheel using Alpine.js  
✅ Real-time updates with Livewire 3  
✅ API-style backend (token & response structure)  
✅ Spin history tracking and balance logging  
✅ Responsive UI with Tailwind CSS and Vite

---

## 🧱 Tech Stack

| Layer     | Technology                    |
|-----------|-------------------------------|
| Backend   | Laravel 11 (API-style)        |
| Frontend  | Livewire 3 + Alpine.js        |
| Styling   | Tailwind CSS + Vite           |
| Database  | MySQL                         |
| Auth      | Laravel Breeze (Livewire mode)|

---

## ✅ Prerequisites

- **PHP**: `^8.2` (Laravel 11 requirement)
- **MySQL**: Any version supporting Laravel 11
- **Composer**: Latest stable
- **Node.js**: `20.19.3` ✅ (LTS, compatible with Laravel 11)
- **npm** or **yarn**

Ensure the following PHP extensions are enabled:
```
openssl, pdo, mbstring, tokenizer, xml, ctype, json, bcmath, fileinfo
```

---

## 🛠️ Installation

```bash
# Step 1: Create Laravel app
composer create-project laravel/laravel:^11 spinning-wheel

cd spinning-wheel

# Step 2: Install Breeze with Livewire stack
composer require laravel/breeze --dev
php artisan breeze:install livewire

# Step 3: Run migrations and seeders
php artisan migrate
php artisan db:seed

# Step 4: Install frontend dependencies
npm install
npm run dev

```

---

## 🔑 .env Setup

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=spinner_wheel_game
DB_USERNAME=root
DB_PASSWORD=
```

---

## 🧪 Default Login Credentials

```
Email    : test@example.com
Password : test1234
```

---

## ▶️ Run the App

```bash
# Start Laravel server
php artisan serve

# Start frontend build watcher
npm run dev
```

---

## 🧹 Optional Artisan Commands

```bash
composer dump-autoload
php artisan optimize:clear
php artisan view:clear
php artisan config:clear
```

---

## 🔁 API Token Auth Example

If you expose an endpoint for token generation:

```http
POST /api/tokens/create

{
  "email": "test@example.com",
  "password": "test1234",
  "device_name": "Browser"
}

Response:
{
  "status": true,
  "data": {
    "token": "xxxxxxxxx"
  },
  "status_code": 200
}
```

---

## 📸 Screenshots (Optional)

You can include screenshots or SVG previews of your spinning wheel or UI here.

---

## 👨‍💻 Author

Developed by **Test User**  
Feel free to fork, extend, or improve this game — contributions welcome!

---

## 📄 License

[MIT](LICENSE)
