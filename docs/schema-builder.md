# Schema Builder

- [Giriş](#giris)
- [Tablo Oluşturma](#tablo-olusturma)
- [Tablo Güncelleme](#tablo-guncelleme)
- [Tablo Silme](#tablo-silme)
- [Sütun Tipleri](#sutun-tipleri)
- [Sütun Modifier'ları](#sutun-modifierlari)
- [İndeksler](#indeksler)
- [Foreign Key](#foreign-key)
- [Tablo Var mı Kontrolü](#tablo-varmi-kontrolu)

---

## Giriş

`App\Core\Schema` sınıfı, tüm desteklenen veritabanı sistemlerine karşı tabloları manipüle etmek için veritabanı-agnostik bir API sağlar. Blueprint DSL, tabloları akıcı (fluent) ve okunabilir bir sözdizimiyle tanımlamayı sağlar.

---

## Tablo Oluşturma

```php
use App\Core\Schema;
use App\Core\Blueprint;

Schema::create('users', function (Blueprint $t) {
    $t->id();
    $t->string('name', 100);
    $t->string('email', 191)->unique();
    $t->string('password');
    $t->string('role', 32)->default('user');
    $t->timestamp('email_verified_at')->nullable();
    $t->timestamps();
});
```

`id()` otomatik `BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY` ekler.
`timestamps()` `created_at` + `updated_at` TIMESTAMP sütunlarını ekler.

---

## Tablo Güncelleme

```php
Schema::table('users', function (Blueprint $t) {
    $t->string('avatar')->nullable();      // ADD COLUMN
    $t->string('phone', 20)->after('email');
});
```

> Şu an `table()` desteği sınırlıdır — kompleks alter işlemleri için ham SQL daha güvenli olabilir.

---

## Tablo Silme

```php
Schema::drop('users');           // hata fırlatır eğer yoksa
Schema::dropIfExists('users');   // sessiz
```

---

## Sütun Tipleri

| Method | SQL Karşılığı |
|---|---|
| `$t->id()` | `BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY` |
| `$t->uuid('id')` | `CHAR(36) PRIMARY KEY` |
| `$t->string($name, $len = 255)` | `VARCHAR($len)` |
| `$t->text($name)` | `TEXT` |
| `$t->longText($name)` | `LONGTEXT` |
| `$t->integer($name)` | `INT` |
| `$t->bigInteger($name)` | `BIGINT` |
| `$t->unsignedBigInteger($name)` | `BIGINT UNSIGNED` |
| `$t->tinyInteger($name)` | `TINYINT` |
| `$t->boolean($name)` | `TINYINT(1)` |
| `$t->decimal($name, $p = 10, $s = 2)` | `DECIMAL($p,$s)` |
| `$t->float($name)` | `FLOAT` |
| `$t->json($name)` | `JSON` |
| `$t->date($name)` | `DATE` |
| `$t->time($name)` | `TIME` |
| `$t->dateTime($name)` | `DATETIME` |
| `$t->timestamp($name)` | `TIMESTAMP NULL` |
| `$t->enum($name, ['a', 'b'])` | `ENUM('a','b')` |
| `$t->timestamps()` | `created_at` + `updated_at` TIMESTAMP |

---

## Sütun Modifier'ları

Akıcı zincirleme:

```php
$t->string('email')->unique();                          // UNIQUE
$t->string('phone')->nullable();                        // NULL'a izin ver
$t->string('role')->default('user');                    // DEFAULT 'user'
$t->boolean('active')->default(1);
$t->string('slug')->after('title');                     // belirli sütundan sonra ekle (mysql)
$t->unsignedBigInteger('user_id')->index();             // ile index ekle
```

> `default()` sadece scalar değer alır. `CURRENT_TIMESTAMP` gibi raw SQL ifadeleri için `timestamps()` veya manuel SQL kullan.

---

## İndeksler

### Tek sütun

```php
$t->string('email')->unique();         // UNIQUE
$t->string('slug')->index();           // INDEX (lookup hızlı)
```

### Kompozit (çoklu sütun)

```php
$t->uniqueIndex(['page', 'block_key']);
$t->index(['user_id', 'created_at']);
```

### İsimli index

```php
$t->index('email', 'idx_users_email');
```

---

## Foreign Key

```php
$t->unsignedBigInteger('user_id');
$t->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
```

> Şu an `foreign()` DSL desteği sınırlıdır; ham SQL ile daha güvenli:

```php
$db = Database::getInstance();
$db->exec("
    ALTER TABLE posts
    ADD CONSTRAINT fk_posts_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE
");
```

---

## Tablo Var mı Kontrolü

```php
if (Schema::hasTable('users')) {
    // ...
}
```

---

**Sonraki:** [Modeller (ORM) →](models.md)
