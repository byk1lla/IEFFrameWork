# Yükseltme Rehberi

- [v1.x → v2.0](#v1x--v20)

---

## v1.x → v2.0

> v2.0 **breaking changes** içerir. Yeni projeler için temiz kurulum, mevcut projeler için manuel migration önerilir.

### Önerilen Yaklaşım: Temiz Kurulum

```bash
# 1. Eski projeyi yedekle
cp -r projem projem-v1-backup
mysqldump -u user -p projem > projem-v1.sql

# 2. Yeni v2 klonu
git clone https://github.com/byk1lla/IEFFrameWork projem-v2
cd projem-v2
composer install

# 3. Config + DB
cp config/database.php.example config/database.php
nano config/database.php   # bilgileri gir

# 4. Migrate (yeni şema)
./ief migrate

# 5. Veriyi taşı (manuel — şema değişti)
mysql -u user -p projem_v2 < migration-script.sql

# 6. Admin oluştur
./ief user:create
```

### Manuel Migration (Aynı DB)

Eğer v1 DB'sini korumak istiyorsan:

1. **Backup al** — `mysqldump`
2. **Yeni migration'ları çalıştır**:
   ```bash
   ./ief migrate
   ```
3. Eski/silinen tabloları manuel sil (`tasks` vb.)
4. Veri shape'i değişen tablolar için custom SQL yaz (e-mail formatı, role değerleri, vb.)
5. Config dosyalarını güncelle (`.env` → `config/*.php`)

### Kod Değişiklikleri

| v1 | v2 |
|---|---|
| `extends BaseController` | `extends App\Core\Controller` |
| `Database::connect()` | `Database::getInstance()` |
| `env('DB_HOST')` | `config('database.host')` |
| `$_SESSION['user']` | `Session::get('auth_user')` |
| `editable_html()` | `editable(..., ['type' => 'html'])` |

### View Değişiklikleri

Eski `@components/alert.php` partial'ları kalktı — Tailwind utility class'ları kullan:

```blade
<!-- v1 -->
@include('components.alert', ['type' => 'success', 'message' => 'Tamam'])

<!-- v2 -->
<div class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm">
    <i class="fa-solid fa-check-circle"></i> Tamam
</div>
```

---

**Sonraki:** [Katkıda Bulunma →](contributing.md)
