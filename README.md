# LSP Project - Sistem Manajemen Perpustakaan

Aplikasi web untuk mengelola katalog buku dan tracking peminjaman di perpustakaan. Sistem dibagi menjadi dua peran pengguna: **Admin (Petugas)** yang mengelola data dan **Member (Anggota)** yang meminjam buku. Dibangun dengan Laravel 12, MySQL, dan Tailwind CSS.

---

## 1. Ringkasan Proyek

Aplikasi **LSP Project** membantu perpustakaan mengelola:
- **Katalog Buku** dengan tracking stok real-time
- **Proses Peminjaman** dengan automated due date (+7 hari)
- **Manajemen Anggota** dengan kontrol akses peminjaman
- **Deteksi Keterlambatan** (OVERDUE & LATE) otomatis
- **Riwayat Peminjaman** untuk audit dan transparansi

Pengguna dibagi menjadi dua peran:
- **Admin (Petugas)**: Mengelola katalog buku, mencatat peminjaman/pengembalian, manajemen anggota
- **Member (Anggota)**: Melihat katalog buku dan riwayat peminjaman pribadi

---

## 2. Metode Pengembangan

**Metode:** Waterfall  
**Alasan:** Kebutuhan relatif jelas, scope kecil, waktu pengerjaan singkat.  
**Tahap:**
1. Analisis → Mengidentifikasi kebutuhan sistem
2. Desain → Membuat ERD, use case, dan class diagram
3. Implementasi → Coding sesuai desain
4. Pengujian → Unit test dan feature test
5. Dokumentasi → README, inline comments, dan dokumentasi teknis

---

## 3. Fitur Utama

### A. Katalog Buku
- Menampilkan daftar semua buku dengan detail (judul, penulis, ISBN, deskripsi, stok)
- Pencarian buku berdasarkan judul, penulis, atau ISBN
- **Stock Alerts** - Warning visual ketika stok tinggal 1 copy

### B. Manajemen Buku (Admin Only)
- **Tambah Buku** - Input form dengan validasi ISBN unik (13 digit)
- **Edit Buku** - Ubah data buku dengan aturan stok (quantity ≥ available)
- **Hapus Buku** - Dengan proteksi: tidak bisa delete jika masih ada peminjaman aktif
- **Stock Tracking** - Available count otomatis berkurang saat peminjaman, bertambah saat dikembalikan

### C. Proses Peminjaman
- **Catat Peminjaman (Admin):**
  - Pilih member dan buku
  - Input custom **borrow_date** (dapat diisi, immutable setelah dibuat)
  - Due_date otomatis terhitung: borrow_date + 7 hari
  - Proteksi: member harus memiliki `can_borrow = true`
  - Validasi: buku harus tersedia (available > 0)

- **Proses Pengembalian (Admin):**
  - Klik tombol "Return" untuk pengembalian
  - Return_date otomatis isi dengan tanggal hari ini
  - Status berubah menjadi "returned"
  - Stok buku otomatis bertambah

### D. Deteksi Keterlambatan (Otomatis)
- **OVERDUE** - Buku belum dikembalikan dan sudah lewat due_date (status: borrowed, hari ini > due_date)
- **LATE** - Buku dikembalikan terlambat (status: returned, return_date > due_date)
- Ditampilkan dengan badge merah untuk visibilitas tinggi

### E. Return Reminders
- Reminder otomatis untuk buku yang harus dikembalikan dalam 3 hari ke depan (due_date - today ≤ 3)
- Ditampilkan dengan highlight biru di halaman peminjaman member

### F. Manajemen Anggota (Admin Only)
- **Daftar Member** - Tampilkan semua anggota dengan informasi:
  - Active Borrowing Count (peminjaman yang masih aktif)
  - Total Borrowings (total riwayat peminjaman)
  - Status can_borrow (dapat/dilarang meminjam)
- **Blok/Izinkan Member:**
  - Toggle akses peminjaman dengan tombol Block/Allow
  - Proteksi: tidak bisa block jika masih ada peminjaman aktif
  - Visual feedback: tombol disabled + tooltip jika ada active borrow

### G. Riwayat Peminjaman (Member)
- Member bisa lihat semua peminjaman mereka sendiri
- Sorted by borrow_date descending (newest first)
- Status indicator: borrowed, returned (on-time), returned (late)
- Back to Catalog button untuk navigasi mudah

### H. Autentikasi & Otorisasi
- Login untuk Admin dan Member dengan email + password
- Password hashing dengan bcrypt (aman)
- Logout button langsung di navbar
- Role-based access control: middleware membatasi akses sesuai role

### I. Proteksi Data
- **Immutable Borrow_date** - Tidak bisa diubah setelah record dibuat
- **Protected Delete** - Tidak bisa delete buku jika ada peminjaman aktif
- **Protected Block** - Tidak bisa block member jika masih ada peminjaman aktif
- **Validation** - Semua input divalidasi (required, format, unique, range)

---

## 4. Coding Guidelines dan Best Practices

**Struktur MVC Laravel:**
```
Routes (web.php) 
  → Controllers (app/Http/Controllers/)
  → Services (app/Services/)
  → Models (app/Models/)
  → Views (resources/views/)
```

**Konvensi Penamaan:**
- Class: PascalCase (e.g., `BookController`, `LibraryService`)
- Method: camelCase (e.g., `createBook()`, `togglePermission()`)
- Variable: camelCase (e.g., `$availableBooks`, `$borrowDate`)
- Database: snake_case (e.g., `user_id`, `borrow_date`)

**Validasi & Logika:**
- Validasi input memakai Laravel validation rules
- Logika bisnis dipisahkan ke service (`LibraryService`) agar controller tetap rapi
- Error handling dilakukan di exception handler atau middleware

---

## 5. Error Handling

**Validasi Input:**
- Error validasi ditampilkan di layout melalui session errors
- Pesan error user-friendly dalam bahasa Indonesia

**Kondisi Khusus:**
- Stok habis: Validasi mencegah peminjaman
- Return_date invalid: Validasi format tanggal
- Member blocked: Validasi mencegah peminjaman
- Buku dengan peminjaman aktif: Tidak bisa dihapus

**Exception Handling:**
- Custom exceptions di `app/Exceptions/AppExceptions.php`
- Classes: `BorrowingException`, `BookException`, `UserException`

---

## 6. Ukuran Performa

Pengukuran lokal menggunakan Laravel Debugbar (optional):
- Response time rata-rata: 195 ms - 1 detik
- Performa tergantung jumlah data dan spesifikasi komputer

**Tips Optimasi:**
- Install Debugbar untuk monitoring: `composer require barryvdh/laravel-debugbar --dev`
- Pastikan APP_DEBUG=true di .env untuk development

---

## 7. Tipe Data dan Struktur Kontrol

**Tipe Data:**
- `string`: nama, judul, penulis, email
- `integer`: ISBN (13 digit), quantity, available
- `date`: borrow_date, due_date, return_date
- `boolean`: can_borrow flag, status enum

**Struktur Kontrol:**
- **Percabangan (if/else):** Validasi status peminjaman, pengecekan stok, role-based access
- **Perulangan (foreach):** Iterasi daftar buku, daftar peminjaman, daftar member
- **Query Builder:** Filtering berdasarkan status, pencarian berdasarkan keyword

---

## 8. Program Sederhana, Prosedur/Fungsi, Array

**Input/Output:**
- Input: HTML form (login, tambah buku, catat peminjaman)
- Output: Data ditampilkan di Blade templates dengan styling Tailwind

**Fungsi/Prosedur:**
- Method controller: `store()`, `update()`, `destroy()`, `togglePermission()`
- Method service: `createBook()`, `recordBorrowing()`, `processReturn()`
- Model methods: `scopeAvailable()`, `isOverdue()`, `isLate()`

**Array:**
- Seeder data (DatabaseSeeder.php) menggunakan array untuk daftar buku
- Response API menggunakan array untuk multiple records

---

## 9. Pemrograman Berorientasi Objek

**Class & Object:**
- `BookController`, `UserController`, `BorrowingController` - Class controller
- `User`, `Book`, `Borrowing` - Model class
- `LibraryService` - Service class untuk business logic

**Property & Method:**
```php
// Example: BookController
class BookController {
    protected $service;  // property
    
    public function store($data) {  // method
        return $this->service->createBook($data);
    }
}
```

**Hak Akses:**
- `public`: Dapat diakses dari mana saja
- `protected`: Dapat diakses dari class dan child class
- `private`: Hanya dapat diakses dalam class yang sama

---

## 10. Akses Basis Data

**CRUD via Eloquent ORM:**
```php
// Create
$book = Book::create($data);

// Read
$books = Book::all();
$book = Book::find($id);

// Update
$book->update($data);

// Delete
$book->delete();
```

**Relasi Database:**
- `User` → `Borrowing` (1 user bisa punya banyak borrowing)
- `Book` → `Borrowing` (1 buku bisa dipinjam banyak kali)
- Query dengan eager loading: `Borrowing::with('user', 'book')->get()`

**Indeks & Unique Constraint:**
- Email: unique (tidak boleh ada email user yang sama)
- ISBN: unique (tidak boleh ada ISBN buku yang sama)

**Koneksi Database:**
- Diatur di `.env` dengan variable: `DB_HOST`, `DB_USERNAME`, `DB_PASSWORD`, `DB_DATABASE`

---

## 11. Pengujian dan Debugging

**Feature Testing:**
- Test untuk controller & routes: `tests/Feature/BookControllerTest.php`, `tests/Feature/BorrowingControllerTest.php`
- Test authentication, authorization, dan CRUD operations

**Manual Testing:**
- Skenario 1: Login → Lihat katalog → Pencarian buku
- Skenario 2: Catat peminjaman → Lihat stok berkurang → Proses pengembalian → Lihat stok bertambah
- Skenario 3: Member lihat riwayat peminjaman dengan status overdue/late/returned

**Debugging Tools:**
- Laravel error messages di browser (jika APP_DEBUG=true)
- Laravel Debugbar (optional): `composer require barryvdh/laravel-debugbar --dev`
- Log files: `storage/logs/laravel.log`

---

## 12. Dokumentasi Teknis

### BookController
```php
index()         // Menampilkan daftar buku dan pencarian
create()        // Form tambah buku
store($data)    // Validasi dan simpan buku baru
edit($book)     // Form edit buku
update($book, $data)  // Update data buku
destroy($book)  // Hapus buku (dengan proteksi)
```

### BorrowingController
```php
index()         // Daftar peminjaman dengan filter status
create()        // Form catat peminjaman baru
store($data)    // Simpan peminjaman (due_date auto +7)
update($id)     // Proses pengembalian
```

### UserController
```php
index()         // Daftar member dengan stats
show($user)     // Detail profil member
togglePermission($user)  // Block/allow member
```

### AuthController
```php
showLogin()     // Tampilkan halaman login
login($request) // Autentikasi user
logout()        // Logout user
```

### LibraryService
```php
createBook($data)           // Create book dengan validasi
updateBook($book, $data)    // Update book dengan proteksi
deleteBook($book)           // Delete book dengan proteksi
recordBorrowing($data)      // Catat peminjaman + auto due_date
processReturn($borrowing)   // Proses pengembalian
toggleUserPermission($user) // Block/allow member
```

### Models
```php
// User Model
- belongsToMany(Borrowing)
- canBorrow property

// Book Model
- hasMany(Borrowing)
- stockAlerts() scope

// Borrowing Model
- belongsTo(User)
- belongsTo(Book)
- isOverdue() method
- isLate() method
```
---

## 13. Reuse Komponen dan Library

- Laravel
- Debugbar(optional)

---

## 14. Setup & Menjalankan Aplikasi

### Prerequisites
- PHP 8.2+
- Composer
- MySQL (via XAMPP)
- Node.js & npm
- Laravel 12 compatible

### Langkah-langkah Instalasi

```bash
# 1. Clone atau download project
cd /Applications/XAMPP/xamppfiles/htdocs/Laravel/"LSP Project"

# 2. Install PHP dependencies
composer install

# 3. Install JavaScript dependencies
npm install

# 4. Copy .env.example ke .env
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Setup database (fresh migration + seed test data)
php artisan migrate:fresh --seed

# 7. Build frontend assets
npm run build

# 8. Jalankan server (di terminal baru)
php artisan serve --port=3000

# 9. Buka di browser
# http://localhost:3000
```

### Konfigurasi Database (.env)
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=demo
DB_USERNAME=root
DB_PASSWORD=
```

### Development Tools (Optional)

**Laravel Debugbar** - Untuk monitoring dan debugging:
```bash
composer require barryvdh/laravel-debugbar --dev
```
Pastikan `APP_DEBUG=true` di `.env`
```

---

## 15. Login Demo

### Admin Account
- Email: `admin@library.com`
- Password: `password`
- Akses: CRUD buku, catat peminjaman, manajemen anggota

### Test Account
- Email: `test@example.com`
- Password: `password`
- Akses: view list buku

---

## 16. Struktur Database

### Users Table
```sql
id (PK)
name (string)
email (string unique)
password (hashed)
role (enum: 'admin', 'member')
can_borrow (boolean, default: true untuk member)
created_at
updated_at
```

### Books Table
```sql
id (PK)
title (string)
author (string)
isbn (string unique, 13 digits)
description (text)
quantity (integer, min 1)
available (integer, <= quantity)
created_at
updated_at
```

### Borrowings Table
```sql
id (PK)
user_id (FK → users.id)
book_id (FK → books.id)
borrow_date (date, default: today)
due_date (date, auto: borrow_date + 7 hari)
return_date (date, nullable)
status (enum: 'borrowed', 'returned')
created_at
updated_at
```

---

## 17. Validasi & Business Logic

### Book Validations
- **Title:** required, max 255 characters
- **Author:** required, max 255 characters
- **ISBN:** required, 13 digits numeric, unique
- **Description:** required, min 10 characters
- **Quantity:** required, min 1, integer; quantity ≥ available

### Borrowing Validations
- **user_id:** required, exists in users table, user.can_borrow = true
- **book_id:** required, exists in books table, available > 0
- **borrow_date:** required(automatic), date format (user dapat input custom)
- **due_date:** auto calculated (borrow_date + 7 hari)
- Tidak bisa record peminjaman untuk member yang blocked

### User Validations
- **Name:** required, string
- **Email:** required, unique, valid email format
- **Password:** required, min 8 characters

### Business Logic Rules
- Peminjaman otomatis due date = borrow_date + 7 hari
- Available count otomatis berkurang saat peminjaman, bertambah saat dikembalikan
- Admin bisa custom borrow_date saat input peminjaman baru
- Member tidak bisa self-borrow (hanya admin yang catat)
- Stock alert merah muncul ketika available = 1
- Return reminder untuk due date ≤ 3 hari ke depan
- Tidak bisa delete buku jika ada borrowing aktif
- Tidak bisa return buku yang sudah dikembalikan
- **Borrow_date immutable:** Tidak bisa diubah
- OVERDUE: status='borrowed' dan today > due_date
- LATE: status='returned' dan return_date > due_date

---

## 19. Dokumentasi Lebih Lanjut

- **ERD Diagram:** `ERD-LSP.drawio.xml` - Visualisasi database schema
- **Blade Templates:** `resources/views/` - UI components dan layouts
- **API Documentation:** Inline comments di controllers dan models
- **Code Examples:** Lihat method-method di `LibraryService` untuk contoh business logic

---