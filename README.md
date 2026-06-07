<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# Setup

## Install Dependencies via Composer

Masuk ke folder project
cd C:\Users\nama_user_laptop\Herd\absensi

1. Install semua dependency Laravel (wajib pertama kali)
composer install

2. QR Code generator
composer require simplesoftwareio/simple-qrcode

3. Image processing (untuk download kartu)
composer require intervention/image:^3.0 -W

4. Import/Export Excel
composer require maatwebsite/excel


## setup awal project

1. Generate APP_KEY
php artisan key:generate

2. Jalankan semua migration
php artisan migrate

3. Clear semua cache
php artisan optimize:clear


## Konfigurasi .env yang wajib diubah

1. APP_NAME=Absensi
2. APP_URL=http://absensi.test

3. DB_CONNECTION=mysql
4. DB_HOST=127.0.0.1
5. DB_PORT=3306
6. DB_DATABASE=absensi_sekolah
7. DB_USERNAME=root
8. DB_PASSWORD=

9. TIMEZONE=Asia/Makassar
10. SESSION_DRIVER=file
11. SESSION_DOMAIN=null


## Buat Akun Admin Pertama

1. php artisan tinker 

2. lalu jalankan:
\App\Models\User::create([
    'username' => 'admin',
    'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
    'role'     => 'admin',
    'kelas'    => null,
]);


## Untuk Deploy via ngrok

1. Download ngrok dari https://ngrok.com/download
2. Lalu jalankan:
ngrok config add-authtoken YOUR_TOKEN
ngrok http 80 --host-header=absensi.test


## Update .env saat menggunakan ngrok

1. APP_URL=https://your-domain.ngrok-free.app
2. SESSION_DOMAIN=your-domain.ngrok-free.app
3. SANCTUM_STATEFUL_DOMAINS=your-domain.ngrok-free.app
4. SESSION_SECURE_COOKIE=false
