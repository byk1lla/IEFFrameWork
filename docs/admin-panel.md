# Admin Paneli

- [Giriş](#giris)
- [Erişim](#erisim)
- [Dashboard](#dashboard)
- [Modüller](#moduller)
- [Layout & Tasarım](#layout-tasarim)
- [Yeni Sayfa Eklemek](#yeni-sayfa-eklemek)

---

## Giriş

IEF, **batteries-included** bir admin paneliyle gelir. Mesajlar, randevular, blog, medya, kullanıcılar, ayarlar, analytics, log görüntüleyici — hepsi `/admin` altında hazır.

Tasarım: Tailwind + Font Awesome + Poppins + DataTables 2.x + ApexCharts + SweetAlert2.

---

## Erişim

1. `/login`'den admin (`superadmin` / `admin` / `editor`) ile gir
2. `/admin` adresine yönlendirilirsin

Tüm `/admin/*` route'ları `AuthMiddleware` ile korumalıdır — login değilsen `/login`'e redirect olursun.

---

## Dashboard

`/admin` — anasayfa. Tipik widget'lar:

- Son 7 gün ziyaretçi grafiği (ApexCharts)
- Yeni mesaj sayısı (badge)
- Yeni randevu sayısı
- En çok ziyaret edilen sayfalar (top 10)
- Hızlı linkler

---

## Modüller

| Modül | URL | Açıklama |
|---|---|---|
| **Mesajlar** | `/admin/messages` | İletişim formundan gelen mesajlar (DataTable + detay + silme) |
| **Randevular** | `/admin/appointments` | Randevu talepleri, durum güncelleme (pending/confirmed/cancelled) |
| **Blog** | `/admin/blog` | Yazılar + AI ile oluştur (Groq) + kategori |
| **Editör** | `/admin/editor` | [Site Editör](site-editor.md)'e yönlendirir (`/site-editor`) |
| **Medya** | `/admin/media` | Yüklenen dosyalar — galeri görünümü + upload + silme |
| **Trafik (Analytics)** | `/admin/analytics` | Ziyaretçi, sayfa görüntüleme, oturum, olay raporları |
| **Loglar** | `/admin/logs` | `storage/logs/*.log` görüntüleyici (filtre + arama) |
| **Kullanıcılar** | `/admin/users` | User CRUD, rol atama |
| **Ayarlar** | `/admin/settings` | 7 sekme: Genel, Sosyal, Görünüm, Mail, SEO, Güvenlik, AI |

---

## Layout & Tasarım

`app/Views/layouts/admin.php`:
- Üstte: breadcrumb + arama + kullanıcı dropdown
- Solda: sticky sidebar (kategorize menü)
- İçerik alanı: `@yield('content')`
- Footer: framework version + status

Sidebar menü `layouts/admin.php` içinde tanımlıdır. Yeni modül eklersen oraya link ekleyebilirsin.

> Renk paleti: `brand` (mavi) + `accent` (cyan). Tailwind config layout'un `<script>` bloğunda.

---

## Yeni Sayfa Eklemek

3 adım:

### 1. Controller

```bash
./ief make:controller Admin/ReportController
```

```php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Response;

class ReportController extends Controller
{
    public function index(): Response
    {
        return $this->view('admin.reports.index', [
            'data' => /* ... */,
        ]);
    }
}
```

### 2. Route

`config/routes.php`'deki `Router::group(['prefix' => '/admin', ...])` içine:

```php
Router::get('/reports', 'Admin\ReportController@index');
```

### 3. View

`app/Views/admin/reports/index.php`:

```blade
@extends('layouts.admin')

@section('title', 'Raporlar')
@section('crumb', 'Raporlar')

@section('content')
<h1 class="text-2xl font-extrabold">Raporlar</h1>
<!-- ... -->
@endsection
```

### 4. (Opsiyonel) Sidebar Link

`layouts/admin.php` içindeki menü array'ine ekle.

---

**Sonraki:** [Site Editör →](site-editor.md)
