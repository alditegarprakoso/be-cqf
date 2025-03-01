# Laravel Project Setup

## 1. Install Dependencies
Pastikan udah install Composer & PHP. Kalau belum, install dulu:

```sh
composer install
```

## 2. Setup Database
Edit `.env` dan sesuaikan konfigurasi database:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=be_cqf
DB_USERNAME=root
DB_PASSWORD=
```

Terus jalanin migrasi:

```sh
php artisan migrate
```

Kalau ada seeder, jalankan:

```sh
php artisan db:seed
```

## 4. Running Server
Jalanin servernya dengan:

```sh
php artisan serve
```

Akses di browser:

```
http://127.0.0.1:8000
```

## 5. Storage & Cache (Opsional)
Kalau butuh, jalankan ini:

```sh
php artisan storage:link
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

---
Sekarang Laravel siap jalan! 🚀
