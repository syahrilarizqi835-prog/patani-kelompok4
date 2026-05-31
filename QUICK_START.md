# 🚀 QUICK START GUIDE - LARAVEL PATANI

## ⚡ INSTALASI SUPER CEPAT (5 MENIT)

### Step 1: Persiapan
```bash
# Pastikan XAMPP sudah terinstall
# Start Apache & MySQL dari XAMPP Control Panel
```

### Step 2: Copy Project
```bash
# Copy folder laravel-patani ke:
C:\xampp\htdocs\patani
```

### Step 3: Install Dependencies
```bash
cd C:\xampp\htdocs\patani
composer install
```

### Step 4: Setup Environment
```bash
# Copy .env dari project atau buat baru
copy .env.example .env

# Generate key
php artisan key:generate
```

### Step 5: Setup Database
```
1. Buka: http://localhost/phpmyadmin
2. Klik tab "Import"
3. Pilih file: database/patani_database.sql
4. Klik "Go"
✅ Database siap!
```

### Step 6: Jalankan Aplikasi
```bash
php artisan serve
```

Buka browser: **http://localhost:8000**

---

## 🔑 LOGIN CREDENTIALS

**Admin:**
- Email: admin@patani.com
- Password: password

**Petani:**
- Email: ahmad@patani.com
- Password: password

---

## 📁 FILE-FILE YANG SUDAH DIBUAT

### ✅ Core Files (31 files)
```
📦 laravel-patani/
│
├── 📄 .env                                    ✅ Environment config
├── 📄 README.md                               ✅ Dokumentasi lengkap
├── 📄 MAPPING_UI_TO_LARAVEL.md                ✅ Panduan mapping UI
│
├── 📂 app/
│   ├── 📂 Http/
│   │   ├── 📂 Controllers/
│   │   │   ├── 📂 Auth/
│   │   │   │   ├── LoginController.php         ✅ Auth login
│   │   │   │   └── RegisterController.php      ✅ Auth register
│   │   │   ├── 📂 Admin/
│   │   │   │   ├── AdminDashboardController.php ✅ Dashboard admin
│   │   │   │   └── PetaniController.php        ✅ CRUD petani
│   │   │   ├── 📂 Petani/
│   │   │   │   ├── SawahController.php         ✅ CRUD sawah
│   │   │   │   └── ForumController.php         ✅ Forum diskusi
│   │   │   └── DashboardController.php         ✅ Dashboard petani
│   │   ├── 📂 Middleware/
│   │   │   └── CheckRole.php                   ✅ Role middleware
│   │   └── Kernel.php                          ✅ HTTP Kernel
│   └── 📂 Models/
│       ├── User.php                            ✅ User model
│       ├── Sawah.php                           ✅ Sawah model
│       ├── Perawatan.php                       ✅ Perawatan model
│       ├── RiwayatPanen.php                    ✅ Riwayat panen
│       ├── PrediksiPanen.php                   ✅ Prediksi model
│       ├── ForumTopic.php                      ✅ Forum topic
│       └── ForumModels.php                     ✅ Forum, Cuaca, dll
│
├── 📂 database/
│   ├── 📂 migrations/
│   │   ├── 2024_01_01_000001_create_users_table.php        ✅
│   │   ├── 2024_01_01_000002_create_sawah_table.php        ✅
│   │   ├── 2024_01_01_000003_create_perawatan_table.php    ✅
│   │   ├── 2024_01_01_000004_create_riwayat_panen_table.php ✅
│   │   ├── 2024_01_01_000005_create_prediksi_panen_table.php ✅
│   │   ├── 2024_01_01_000006_create_forum_tables.php       ✅
│   │   ├── 2024_01_01_000007_create_cuaca_table.php        ✅
│   │   ├── 2024_01_01_000008_create_chatbot_conversations_table.php ✅
│   │   └── 2024_01_01_000009_create_pengaturan_table.php   ✅
│   ├── 📂 seeders/
│   │   ├── DatabaseSeeder.php                  ✅ Main seeder
│   │   ├── UserSeeder.php                      ✅ User dummy data
│   │   └── SawahSeeder.php                     ✅ Sawah dummy data
│   └── patani_database.sql                     ✅ SQL dump lengkap
│
├── 📂 resources/views/
│   └── 📂 layouts/
│       ├── app.blade.php                       ✅ Main layout
│       └── dashboard.blade.php                 ✅ Dashboard layout
│
└── 📂 routes/
    └── web.php                                 ✅ All routes
```

---

## 📊 STRUKTUR DATABASE (9 Tabel)

```sql
✅ users                 -- User & Petani
✅ sawah                 -- Data sawah
✅ perawatan             -- Aktivitas perawatan
✅ riwayat_panen         -- History panen
✅ prediksi_panen        -- Prediksi hasil
✅ forum_topics          -- Topik forum
✅ forum_replies         -- Balasan forum
✅ forum_likes           -- Like system
✅ cuaca                 -- Data cuaca
✅ chatbot_conversations -- Chat history
✅ pengaturan            -- System settings
```

---

## 🎯 FITUR YANG SUDAH SIAP

### ✅ Fitur Backend (Fully Functional)
- [x] Authentication (Login & Register)
- [x] Role-based access (Admin & Petani)
- [x] CRUD Sawah
- [x] CRUD Petani (Admin)
- [x] Dashboard Statistics
- [x] Forum Discussion
- [x] Database dengan relasi lengkap
- [x] Middleware protection
- [x] Validation
- [x] Soft Deletes

### ⚠️ Perlu Dilengkapi (View Templates)
- [ ] View: Login page (template sudah ada di MAPPING doc)
- [ ] View: Register page
- [ ] View: Dashboard petani
- [ ] View: Data sawah page
- [ ] View: Prediksi panen
- [ ] View: Perawatan
- [ ] View: Cuaca
- [ ] View: Chatbot
- [ ] View: Forum diskusi
- [ ] View: Riwayat panen
- [ ] View: Admin dashboard
- [ ] View: Admin petani page
- [ ] View: Admin sawah
- [ ] View: Landing page

---

## 🛠️ CARA MELENGKAPI VIEW

### Template View Login (Contoh)
File: `resources/views/auth/login.blade.php`

```blade
@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 p-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-lg shadow p-8">
            <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>
            
            <form action="{{ route('login') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Email</label>
                    <input type="email" name="email" 
                           class="w-full px-4 py-2 border rounded-lg"
                           value="{{ old('email') }}" required>
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Password</label>
                    <input type="password" name="password"
                           class="w-full px-4 py-2 border rounded-lg" required>
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <button type="submit" 
                        class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700">
                    Login
                </button>
            </form>
            
            <p class="mt-4 text-center text-sm">
                Belum punya akun? 
                <a href="{{ route('register') }}" class="text-green-600 hover:underline">
                    Daftar
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
```

**Copy template ini untuk semua halaman lain, sesuaikan dengan kebutuhan!**

---

## 📋 CONTROLLERS YANG PERLU DIBUAT

Beberapa controller masih perlu ditambahkan:

```php
// app/Http/Controllers/Petani/PrediksiController.php
// app/Http/Controllers/Petani/PerawatanController.php
// app/Http/Controllers/Petani/CuacaController.php
// app/Http/Controllers/Petani/ChatbotController.php
// app/Http/Controllers/Petani/RiwayatController.php

// app/Http/Controllers/Admin/SawahController.php
// app/Http/Controllers/Admin/LaporanController.php
// app/Http/Controllers/Admin/PengaturanController.php
// app/Http/Controllers/Admin/ForumController.php
```

**Template sederhana untuk controller:**

```php
<?php

namespace App\Http\Controllers\Petani;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NamaController extends Controller
{
    public function index()
    {
        // Logic here
        $data = []; // Get data from database
        
        return view('dashboard.nama-view', compact('data'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            // validation rules
        ]);
        
        // Save to database
        
        return redirect()->back()->with('success', 'Berhasil!');
    }
}
```

---

## 🔥 TIPS DEVELOPMENT

### 1. Testing Routes
```bash
php artisan route:list
```

### 2. Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### 3. Database Reset
```bash
php artisan migrate:fresh --seed
```

### 4. Debug
```php
dd($variable);        // Dump and die
dump($variable);      // Just dump
logger($variable);    // Log to file
```

### 5. View Debugging
```blade
@dump($data)          <!-- Dump di view -->
{{ var_dump($data) }} <!-- Var dump -->
```

---

## ⚡ WORKFLOW DEVELOPMENT

1. **Buat Controller** → Copy template di atas
2. **Buat Route** → Edit `routes/web.php`
3. **Buat View** → Copy layout dari dashboard.blade.php
4. **Test** → Akses via browser
5. **Fix Bugs** → Check error di browser/logs

---

## 🎨 STYLING (Tailwind CSS)

Sudah include via CDN di layout:
```html
<script src="https://cdn.tailwindcss.com"></script>
```

Common Tailwind Classes:
```
Container: max-w-7xl mx-auto px-4
Button: bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700
Input: border border-gray-300 rounded-lg px-4 py-2 w-full
Card: bg-white rounded-lg shadow-lg p-6
```

---

## 📞 TROUBLESHOOTING CEPAT

**Error: Class not found**
```bash
composer dump-autoload
```

**Error: Database connection**
- Check .env file
- Pastikan MySQL running
- Pastikan database patani_db exists

**Error: Permission denied**
```bash
chmod -R 777 storage
chmod -R 777 bootstrap/cache
```

**Halaman blank/error 500**
- Check `storage/logs/laravel.log`
- Enable debug: `APP_DEBUG=true` di .env

---

## ✨ NEXT STEPS

1. ✅ Setup database (DONE)
2. ✅ Setup routes (DONE)
3. ✅ Setup controllers (DONE sebagian)
4. ⏳ Lengkapi view templates
5. ⏳ Lengkapi remaining controllers
6. ⏳ Implement JavaScript/Charts
7. ⏳ Testing semua fitur
8. ⏳ Deploy ke production

---

## 💡 BANTUAN TAMBAHAN

Semua template dan contoh code ada di:
- `README.md` - Dokumentasi lengkap
- `MAPPING_UI_TO_LARAVEL.md` - Mapping UI detail
- `database/patani_database.sql` - Database lengkap

**You've got this! 🚀**

Happy Coding! 🌾
