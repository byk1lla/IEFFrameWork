# Migration'lar

- [Giriş](#giris)
- [Migration Oluşturma](#migration-olusturma)
- [Migration Yapısı](#migration-yapsi)
- [Migration Çalıştırma](#migration-altrma)
- [Geri Alma (Rollback)](#geri-alma-rollback)
- [Fresh Migrate](#fresh-migrate)
- [Status](#status)

---

## Giriş

Migration'lar veritabanı için versiyon kontrolü gibidir; takımının uygulamanın veritabanı şema tanımını paylaşmasına ve birlikte düzenlemesine olanak tanır. Yeni bir sütun eklemek isteyen bir takım arkadaşıyla problem yaşadıysan, migration'lar bu problemi çözer.

Tüm migration dosyaları `app/Database/Migrations/` dizininde saklanır.

---

## Migration Oluşturma

```bash
./ief make:migration create_posts_table
```

Bu, `app/Database/Migrations/m20260525_143012_create_posts_table.php` dosyasını standart stub ile oluşturur.

> Dosya adı `m<YYYYMMDD>_<HHMMSS>_<açıklama>` formatındadır. Tarih damgası migration'ların **çalıştırma sırasını** belirler.

---

## Migration Yapısı

Bir migration sınıfı iki method içerir: `up` ve `down`.

```php
<?php

namespace App\Database\Migrations;

use App\Core\Blueprint;
use App\Core\Database;
use App\Core\Schema;

class m20260525_143012_create_posts_table
{
    public function up(Database $db): void
    {
        Schema::create('posts', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id');
            $t->string('title', 200);
            $t->string('slug', 220)->unique();
            $t->longText('body');
            $t->string('cover')->nullable();
            $t->boolean('published')->default(0);
            $t->timestamp('published_at')->nullable();
            $t->timestamps();

            $t->index('published');
        });
    }

    public function down(Database $db): void
    {
        Schema::dropIfExists('posts');
    }
}
```

> Detay: [Schema Builder →](schema-builder.md)

---

## Migration Çalıştırma

Tüm bekleyen migration'ları çalıştır:

```bash
./ief migrate
```

```
→ m20260524_000001_create_users_table
  ✓ migrated
→ m20260524_000010_create_messages_table
  ✓ migrated
→ m20260525_143012_create_posts_table
  ✓ migrated
Toplam 3 migration çalıştırıldı (batch #4).
```

Migration'lar **idempotent**'tir — daha önce çalıştırılanlar tekrar çalıştırılmaz. `migrations` adında bir kayıt tablosu, hangi dosyaların ne zaman çalıştığını takip eder.

---

## Geri Alma (Rollback)

Son batch'i geri al:

```bash
./ief migrate:rollback
```

Birden fazla batch:

```bash
./ief migrate:rollback 3   # son 3 batch
```

`down()` method'undaki tanımları çalıştırır — şemayı önceki haline getirir.

> **Uyarı:** Rollback yıkıcıdır. Migration'da `Schema::dropIfExists()` kullanırsan tablo + tüm verisi gider. Production'da rollback yerine **yeni bir migration** ile düzeltme yapmayı tercih et.

---

## Fresh Migrate

Tüm tabloları sil ve sıfırdan kur:

```bash
./ief migrate:fresh
```

> **Asla** production'da kullanma — tüm veriyi siler!

İdeal dev kullanımı: seed dataları yenilemek için `migrate:fresh && ./ief db:seed`.

---

## Status

Hangi migration'lar çalıştı, hangileri bekliyor?

```bash
./ief migrate:status
```

```
+-------+----------------------------------------------+--------+--------------+
| Batch | Migration                                    | Durum  | Çalışma Tarihi |
+-------+----------------------------------------------+--------+--------------+
| 1     | m20260524_000001_create_users_table          | ✓ Done | 2026-05-24    |
| 1     | m20260524_000010_create_messages_table       | ✓ Done | 2026-05-24    |
| -     | m20260526_120000_add_avatar_to_users         | ⧖ Wait | -             |
+-------+----------------------------------------------+--------+--------------+
```

---

**Sonraki:** [Schema Builder →](schema-builder.md)
