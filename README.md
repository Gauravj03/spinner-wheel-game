# 🎯 Spinning Wheel Game — Laravel 11, Livewire 3, Alpine.js

A fun, interactive spinning wheel game where users can log in, top up their balance, spin to win (or lose!) credits, and track results in real time — built using **Laravel 11**, **Livewire 3**, and **Alpine.js**.

---

## 📖 User Story

> As a user, I want to play a spinning wheel game where I can log in, top up my balance, spin the wheel, and have the results stored and displayed.

---

## 🚀 Features

- ✅ User authentication (Laravel Breeze + Livewire)
- ✅ Balance system with top-up option
- ✅ Interactive spinning wheel using Alpine.js
- ✅ Real-time updates via Livewire 3
- ✅ Token-based API backend (REST-style)
- ✅ Spin history tracking with database logging
- ✅ Modern UI using Tailwind CSS & Vite

---

## 🧱 Tech Stack

| Layer     | Technology                     |
|-----------|--------------------------------|
| Backend   | Laravel 11 (API-style)         |
| Frontend  | Livewire 3 + Alpine.js         |
| Styling   | Tailwind CSS + Vite            |
| Database  | MySQL                          |
| Auth      | Laravel Breeze (Livewire mode) |

---

## ✅ Prerequisites & Setup

Make sure the following tools are installed on your system:

| Tool       | Version / Link                                                                 |
|------------|----------------------------------------------------------------------------------|
| **PHP**    | ^8.2 — [Install PHP](https://www.php.net/downloads)                            |
| **Composer** | Latest — [Get Composer](https://getcomposer.org/download/)                   |
| **Node.js** | ^20.19.3 — [Download Node.js (LTS)](https://nodejs.org/)                      |
| **npm**     | Installed with Node.js                                                         |
| **MySQL**   | Any recent version supporting Laravel 11                                       |

<details>
<summary>📦 Required PHP Extensions</summary>

```
openssl, pdo, mbstring, tokenizer, xml, ctype, json, bcmath, fileinfo
```
</details>

---

## ⚙️ Installation Steps

> 📝 Tip: You can also clone this repository if you're not creating from scratch.

```bash
# Clone the repository (if shared)
git clone https://github.com/your-username/spinning-wheel.git
cd spinning-wheel

# Step 1: Install PHP dependencies
composer install

# Step 2: Install Breeze (if needed)
composer require laravel/breeze --dev
php artisan breeze:install livewire

# Step 3: Setup environment file
cp .env.example .env

# Step 4: Set database credentials in `.env` (see below)

# Step 5: Generate app key
php artisan key:generate

# Step 6: Run migrations and optional seeders
php artisan migrate --seed

# Step 7: Install and build frontend assets
npm install
npm run dev
```

---

## 🔑 .env Example

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=spinner_wheel_game
DB_USERNAME=root
DB_PASSWORD=
```

---

## 🧪 Default Test Credentials

```bash
Email    : test@example.com
Password : test1234
```

You can customize or seed your own users via `DatabaseSeeder`.

---

## ▶️ Run the App

```bash
# Start Laravel development server
php artisan serve

# Watch frontend files (CSS, JS)
npm run dev
```

Open in browser: [http://localhost:8000](http://localhost:8000)

---

## 🔁 API Example (Token-Based Auth)

> Assuming you expose `/api/tokens/create` for issuing personal access tokens:

```http
POST /api/tokens/create

{
  "email": "test@example.com",
  "password": "test1234",
  "device_name": "Browser"
}
```

**Sample Response**
```json
{
  "status": true,
  "data": {
    "token": "xxxxxxxxxxxxxxxxxxxxx"
  },
  "status_code": 200
}
```

Use this token as a Bearer token in `Authorization` headers.

---

## 🧹 Useful Artisan Commands

```bash
composer dump-autoload
php artisan optimize:clear
php artisan config:clear
php artisan migrate:fresh --seed
```

---

## 📸 Screenshots (Optional)

![[Spinning Wheel Dashboard](https://github.com/Gauravj03/spinner-wheel-game/tree/main/public/images/spinner_dashboard.png)

![Spinning Wheel Game](https://github.com/Gauravj03/spinner-wheel-game/tree/main/public/images/spinner_wheel_game.png)

![Spinn History](https://github.com/Gauravj03/spinner-wheel-game/tree/main/public/images/spinner_history.png)

---

## 👨‍💻 Author

Developed by **Gaurav Jain**  
Web Developer with experience in **Laravel**, **Livewire**, and full-stack development.  
**Skills**: PHP MVC Developer, Laravel, Livewire, MySQL, REST APIs, JavaScript, Alpine.js, Tailwind CSS


---

## 📄 License

[MIT License](LICENSE)