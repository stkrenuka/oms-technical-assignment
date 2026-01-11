# 📦 Order Management System (OMS)

A full-stack **Order Management System** built using **Laravel (REST API backend)** and **Vue 3 (frontend SPA)**.
The system supports secure authentication, order handling, resumable file uploads, and role-based access control.

---

## 🚀 Tech Stack

### Backend
- Laravel 10+
- Laravel Sanctum (API Authentication)
- MySQL
- Queue Jobs
- Chunk-based / Resumable File Uploads

### Frontend
- Vue 3
- Pinia (State Management)
- Axios
- Tailwind CSS

---

## 📁 Project Structure

oms/
├── backend/ # Laravel API
└── frontend/ # Vue 3 SPA


---

## ⚙️ Backend Setup (Laravel)

### 1️⃣ Install dependencies
```bash
cd backend
composer install


Environment setup
cp .env.example .env
php artisan key:generate

Update .env:

DB_DATABASE=oms
DB_USERNAME=root
DB_PASSWORD=


Run migrations & seeders
php artisan migrate
php artisan db:seed


Create storage link
php artisan storage:link

Start backend server
php artisan serve


Backend URL:http://127.0.0.1:8000


Frontend Setup (Vue 3)

Install dependencies

cd frontend
npm install


2️⃣ Environment variables
VITE_API_BASE_URL=http://localhost:8000/api
VITE_API_URL=http://localhost:8000


Run frontend server
npm run dev

Frontend URL:http://localhost:5173

Versioning
v1.0.0   Initial release
v1.0.1   Patch update
v1.1.0   Feature update
v2.0.0   Breaking changes


Common Commands
php artisan migrate:fresh --seed
php artisan queue:work
php artisan storage:link
php artisan db:seed --class=UserSeeder


## 🔑 Example User Credentials

Use the following credentials to access the system:

### 👤 Admin
- **Email:** `admin@example.com`
- **Password:** `password`

### 👤 Customer
- **Email:** `customer@example.com`
- **Password:** `password`
