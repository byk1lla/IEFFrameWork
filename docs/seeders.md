# Seeder'lar

- [Giriş](#giris)
- [Seeder Oluşturma](#seeder-olusturma)
- [Çalıştırma](#calistirma)

---

## Giriş

Seeder, test/development DB'sine örnek veri eklemek için kullanılır. Production'da nadiren çalıştırılır.

---

## Seeder Oluşturma

```bash
./ief make:seeder DefaultRolesSeeder
```

`app/Database/Seeders/DefaultRolesSeeder.php`:

```php
<?php

namespace App\Database\Seeders;

use App\Core\Database;

class DefaultRolesSeeder
{
    public function run(Database $db): void
    {
        $db->execute("INSERT IGNORE INTO post_categories (name, slug, color) VALUES (?, ?, ?)", ['Teknoloji', 'teknoloji', '#3b82f6']);
        $db->execute("INSERT IGNORE INTO post_categories (name, slug, color) VALUES (?, ?, ?)", ['Tasarım', 'tasarim', '#ec4899']);
        $db->execute("INSERT IGNORE INTO post_categories (name, slug, color) VALUES (?, ?, ?)", ['Genel', 'genel', '#64748b']);
    }
}
```

---

## Çalıştırma

Tüm seeder'lar:

```bash
./ief db:seed
```

CLI `app/Database/Seeders/` altındaki tüm `.php` dosyalarını bulup `run()` method'larını çalıştırır.

> Belirli bir seeder'ı tek başına çalıştırma desteği henüz yok — gerekirse `make:seeder` ile yeni dosya oluştur ve sırasını dosya adıyla belirle.

`INSERT IGNORE` kullanmak idempotent olmasını sağlar — aynı seeder ikinci kez çalıştırıldığında duplicate hata vermez.

---

**Sonraki:** [Authentication →](authentication.md)
