# MAPPING UI NEXT.JS KE LARAVEL BLADE

## 📋 OVERVIEW
Dokumen ini menjelaskan bagaimana setiap halaman dan komponen dari UI Next.js/React dimapping ke struktur Laravel MVC (Model-View-Controller) dengan Blade Templates.

---

## 🗺️ STRUKTUR MAPPING

### 1. LANDING PAGE

**Next.js:**
```
app/page.tsx
├── components/landing/navbar.tsx
├── components/landing/hero-section.tsx
├── components/landing/features-section.tsx
├── components/landing/benefits-section.tsx
├── components/landing/about-section.tsx
├── components/landing/contact-section.tsx
└── components/landing/footer.tsx
```

**Laravel:**
```
Route: GET / → LandingController@index (atau langsung view)
View: resources/views/landing/index.blade.php
```

**Blade Template:**
```php
@extends('layouts.app')

@section('content')
    @include('landing.partials.navbar')
    @include('landing.partials.hero')
    @include('landing.partials.features')
    @include('landing.partials.benefits')
    @include('landing.partials.about')
    @include('landing.partials.contact')
    @include('landing.partials.footer')
@endsection
```

---

### 2. AUTHENTICATION

#### Login Page

**Next.js:**
```
app/login/page.tsx
- useState untuk form handling
- useRouter untuk redirect
- Client-side validation
```

**Laravel:**
```
Routes:
- GET  /login  → LoginController@showLoginForm
- POST /login  → LoginController@login

Controller: app/Http/Controllers/Auth/LoginController.php
View: resources/views/auth/login.blade.php
Validation: Server-side dengan Request validation
Session: Laravel session management
```

**Key Differences:**
- Next.js menggunakan client state → Laravel menggunakan session & old() helper
- Next.js routing via useRouter → Laravel redirect dengan redirect()->route()
- React form submission → Laravel CSRF protection dengan @csrf

#### Register Page

**Next.js:**
```
app/register/page.tsx
- Form dengan multiple fields
- Password confirmation
- Role selection (admin/petani)
```

**Laravel:**
```
Routes:
- GET  /register → RegisterController@showRegistrationForm  
- POST /register → RegisterController@register

View: resources/views/auth/register.blade.php
Validation: 
- required
- email unique
- password confirmed
- min/max rules
```

---

### 3. DASHBOARD PETANI

#### Dashboard Index

**Next.js:**
```
app/dashboard/page.tsx
- Chart.js untuk grafik produksi
- Recharts untuk visualisasi
- Data fetching dengan dummy data
- Weather widget
```

**Laravel:**
```
Route: GET /dashboard → DashboardController@index

Controller Logic:
- Query sawah milik user
- Aggregate data produksi dari RiwayatPanen
- Query cuaca dari database/API
- Calculate statistics

View: resources/views/dashboard/index.blade.php
- Chart.js via CDN
- Data passed dari controller via compact()
- Blade foreach untuk loop data
- @json untuk pass PHP array to JavaScript
```

**Data Flow:**
```php
// Controller
$productionData = RiwayatPanen::whereHas('sawah', function($query) {
    $query->where('user_id', auth()->id());
})->get();

return view('dashboard.index', compact('productionData'));

// Blade
<script>
const productionData = @json($productionData);
// Use in Chart.js
</script>
```

#### Data Sawah

**Next.js:**
```
app/dashboard/sawah/page.tsx
- useState untuk manage list
- Modal dialog untuk CRUD
- Table component
- Form validation
```

**Laravel:**
```
Routes:
- GET    /dashboard/sawah          → SawahController@index
- POST   /dashboard/sawah          → SawahController@store
- PUT    /dashboard/sawah/{id}     → SawahController@update
- DELETE /dashboard/sawah/{id}     → SawahController@destroy

Controller: app/Http/Controllers/Petani/SawahController.php
View: resources/views/dashboard/sawah.blade.php

Features:
- CRUD operations
- Form validation
- SoftDeletes
- Relationship dengan User
```

**Modal Implementation:**
```blade
<!-- Next.js: useState + Dialog component -->
<!-- Laravel: Alpine.js atau Bootstrap Modal -->

<div x-data="{ open: false }">
    <button @click="open = true">Tambah Sawah</button>
    
    <div x-show="open" class="modal">
        <form action="{{ route('dashboard.sawah.store') }}" method="POST">
            @csrf
            <!-- form fields -->
        </form>
    </div>
</div>
```

#### Prediksi Panen

**Next.js:**
```
app/dashboard/prediksi/page.tsx
- Complex form inputs
- Calculation logic
- Result display dengan charts
```

**Laravel:**
```
Route: 
- GET  /dashboard/prediksi → PrediksiController@index
- POST /dashboard/prediksi → PrediksiController@predict

Controller Logic:
- Input validation
- Prediction algorithm
- Store to prediksi_panen table
- Return hasil prediksi

View: resources/views/dashboard/prediksi.blade.php
```

**Algoritma Prediksi:**
```php
// Controller
public function predict(Request $request) {
    $validated = $request->validate([...]);
    
    // Simple prediction logic
    $luasLahan = $validated['luas'];
    $kondisiTanah = $validated['kondisi_tanah'];
    $jenisPadi = $validated['jenis_padi'];
    
    $baseYield = 6.5; // ton/ha
    $multiplier = match($kondisiTanah) {
        'subur' => 1.15,
        'sedang' => 1.0,
        'kurang' => 0.85,
    };
    
    $prediksiHasil = $luasLahan * $baseYield * $multiplier;
    
    PrediksiPanen::create([
        'sawah_id' => $validated['sawah_id'],
        'prediksi_hasil' => $prediksiHasil,
        'confidence_level' => 85,
        ...
    ]);
    
    return back()->with('prediction', $prediksiHasil);
}
```

#### Forum Diskusi

**Next.js:**
```
app/dashboard/forum/page.tsx
- Topic list dengan search & filter
- Create topic modal
- Reply system
- Like functionality
```

**Laravel:**
```
Routes:
- GET  /dashboard/forum          → ForumController@index
- POST /dashboard/forum          → ForumController@store
- POST /dashboard/forum/{id}/reply → ForumController@reply
- POST /dashboard/forum/{id}/like  → ForumController@like

Models:
- ForumTopic (hasMany replies)
- ForumReply (belongsTo topic)
- ForumLike (polymorphic)

View: resources/views/dashboard/forum.blade.php
```

**Like System (Polymorphic):**
```php
// Model ForumLike
public function likeable() {
    return $this->morphTo();
}

// Usage
$topic->likesRelation()->create([
    'user_id' => auth()->id()
]);

// Blade
@if($topic->likesRelation()->where('user_id', auth()->id())->exists())
    <button>Unlike</button>
@else
    <button>Like</button>
@endif
```

---

### 4. ADMIN DASHBOARD

#### Admin Index

**Next.js:**
```
app/admin/page.tsx
- Multiple chart types (Bar, Line, Pie)
- Statistics cards
- Recent activities list
- Real-time updates (polling)
```

**Laravel:**
```
Route: GET /admin → AdminDashboardController@index

Controller:
- Count total petani, sawah, produksi
- Aggregate monthly data
- Recent activities log
- Variety distribution

View: resources/views/admin/index.blade.php
Charts: Chart.js for consistency
```

#### Manajemen Petani

**Next.js:**
```
app/admin/petani/page.tsx
- DataTable dengan search, filter, pagination
- CRUD operations
- Detail view modal
- Status toggle
```

**Laravel:**
```
Routes:
- GET    /admin/petani     → PetaniController@index
- POST   /admin/petani     → PetaniController@store
- PUT    /admin/petani/{id} → PetaniController@update
- DELETE /admin/petani/{id} → PetaniController@destroy
- GET    /admin/petani/{id} → PetaniController@show

Features:
- Pagination (10 per page)
- Search (name, email, NIK)
- Soft deletes
- Validation
```

**Pagination:**
```php
// Controller
$petani = User::where('role', 'petani')
    ->when($request->search, function($q, $search) {
        return $q->where('name', 'like', "%{$search}%");
    })
    ->paginate(10);

// Blade
@foreach($petani as $p)
    <!-- row -->
@endforeach

{{ $petani->links() }}
```

---

## 🔄 COMPONENT MAPPING

### UI Components (shadcn/ui) → Laravel Blade

| Next.js Component | Laravel Implementation |
|------------------|------------------------|
| `<Card>` | Bootstrap card atau Tailwind classes |
| `<Button>` | `<button class="btn">` |
| `<Input>` | `<input class="form-control">` |
| `<Select>` | `<select>` dengan options loop |
| `<Dialog>` | Bootstrap Modal atau Alpine.js |
| `<Table>` | HTML `<table>` dengan Blade loops |
| `<Badge>` | `<span class="badge">` |
| `<Alert>` | Session flash messages |

### Example: Card Component

**React (Next.js):**
```tsx
<Card>
    <CardHeader>
        <CardTitle>Title</CardTitle>
    </CardHeader>
    <CardContent>
        Content here
    </CardContent>
</Card>
```

**Blade:**
```blade
<div class="bg-white rounded-lg shadow p-6">
    <div class="mb-4">
        <h3 class="text-lg font-semibold">Title</h3>
    </div>
    <div>
        Content here
    </div>
</div>
```

---

## 📊 DATA FLOW COMPARISON

### Next.js (Client-Side)
```
Component → useState → API Call → Update State → Re-render
```

### Laravel (Server-Side)
```
Route → Controller → Model → Database → View → HTML Response
```

### Hybrid Approach (Laravel + AJAX)
```
Blade View → JavaScript/Alpine → Fetch API → Laravel Route → JSON Response → Update DOM
```

---

## 🎨 STYLING

### Next.js:
- Tailwind CSS classes
- CSS Modules
- styled-components

### Laravel:
- **Option 1:** Tailwind CSS via CDN (quick setup)
- **Option 2:** Laravel Mix untuk compile Tailwind
- **Option 3:** Bootstrap 5 (traditional)

**Recommended:** Tailwind CSS CDN untuk mempertahankan styling yang sama

```html
<script src="https://cdn.tailwindcss.com"></script>
```

---

## 🔐 AUTHENTICATION & AUTHORIZATION

### Next.js Approach:
```typescript
const router = useRouter();

if (formData.email.includes("admin")) {
    router.push("/admin");
} else {
    router.push("/dashboard");
}
```

### Laravel Approach:
```php
// LoginController
if (Auth::attempt($credentials)) {
    $user = Auth::user();
    
    if ($user->role === 'admin') {
        return redirect('/admin');
    }
    
    return redirect('/dashboard');
}

// Middleware Protection
Route::middleware(['auth', 'role:petani'])->group(function() {
    // Petani routes
});

Route::middleware(['auth', 'role:admin'])->group(function() {
    // Admin routes
});
```

---

## 📦 DEPENDENCY MAPPING

| Next.js Package | Laravel Equivalent |
|----------------|-------------------|
| react-hook-form | Laravel Validation |
| axios | Guzzle HTTP Client |
| recharts | Chart.js |
| next/router | Laravel Routing |
| @tanstack/react-query | Eloquent ORM |
| zod | Validation Rules |

---

## 🔧 INTERACTIVITY

### Next.js (React Hooks):
```typescript
const [isOpen, setIsOpen] = useState(false);
const [data, setData] = useState([]);

useEffect(() => {
    fetchData();
}, []);
```

### Laravel (Alpine.js):
```html
<div x-data="{ 
    isOpen: false, 
    data: @json($data) 
}">
    <button @click="isOpen = !isOpen">Toggle</button>
</div>
```

### Laravel (Vanilla JS):
```blade
<script>
let data = @json($data);

document.getElementById('btn').addEventListener('click', () => {
    // Handle click
});
</script>
```

---

## ✅ CHECKLIST KONVERSI

- [x] Routes setup
- [x] Controllers created
- [x] Models & relationships
- [x] Migrations
- [x] Seeders
- [x] Blade layouts
- [x] Authentication system
- [x] Middleware (role checking)
- [x] CRUD functionality
- [x] Validation
- [ ] View templates (perlu dilengkapi semua halaman)
- [ ] JavaScript interactivity
- [ ] Chart implementation
- [ ] File upload handling
- [ ] API integration (weather)
- [ ] Email notifications
- [ ] Export functionality (PDF/Excel)

---

## 📝 NOTES

1. **State Management:** Next.js menggunakan React state, Laravel menggunakan session & database
2. **Real-time Features:** Next.js bisa pakai websockets, Laravel bisa pakai Laravel Echo + Pusher
3. **File Structure:** Lebih terorganisir di Next.js, Laravel lebih convention-based
4. **Type Safety:** TypeScript di Next.js, PHP 8+ type hints di Laravel
5. **Performance:** Next.js SSR/SSG, Laravel caching & query optimization

---

Developed for seamless UI migration from Next.js to Laravel 🚀
