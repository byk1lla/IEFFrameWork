# Veritabanı: Başlangıç

- [Giriş](#giris)
- [Konfigürasyon](#konfigurasyon)
    - [MySQL](#mysql)
    - [SQLite](#sqlite)
- [Veritabanına Bağlanma](#veritabanina-baglanma)
- [Sorgu Çalıştırma](#sorgu-altrma)
    - [SELECT](#select)
    - [INSERT](#insert)
    - [UPDATE](#update)
    - [DELETE](#delete)
    - [Raw SQL](#raw-sql)
- [Transaction](#transaction)
- [Logging](#logging)

---

## Giriş

Hemen hemen tüm modern web uygulamaları bir veritabanıyla etkileşim kurar. IEF, ham SQL ve [ORM (Modeller)](models.md) aracılığıyla veritabanlarıyla etkileşimi son derece basit hale getirir.

Şu anda IEF iki resmi sürücüyü destekler:

- **MySQL** 5.7+ / MariaDB 10.6+
- **SQLite** 3.35+

---

## Konfigürasyon

`config/database.php`:

### MySQL

```php
return [
    'driver'   => 'mysql',
    'host'     => '127.0.0.1',
    'port'     => 3306,
    'database' => 'ief_framework',
    'username' => 'ief_framework',
    'password' => 'gizli',
    'charset'  => 'utf8mb4',
    'collation'=> 'utf8mb4_unicode_ci',
];
```

### SQLite

```php
return [
    'driver' => 'sqlite',
    'path'   => __DIR__ . '/../storage/db.sqlite',
];
```

Dosyayı manuel oluşturmana gerek yok — `./ief migrate` ilk çalıştırmada üretir.

---

## Veritabanına Bağlanma

PDO singleton; ilk erişimde lazy initialize edilir:

```php
use App\Core\Database;

$db = Database::getInstance();
$pdo = $db->pdo();   // ham PDO instance
```

---

## Sorgu Çalıştırma

### SELECT

Tek satır:

```php
$user = $db->fetch("SELECT * FROM users WHERE id = ?", [42]);
// ['id' => 42, 'name' => 'Efe', ...] veya null
```

Çoklu satır:

```php
$users = $db->fetchAll("SELECT * FROM users WHERE role = ?", ['admin']);
// [['id' => 1, ...], ['id' => 2, ...]]
```

Tek değer (column):

```php
$count = $db->fetchColumn("SELECT COUNT(*) FROM users");
// 42
```

### INSERT

```php
$db->execute(
    "INSERT INTO users (name, email, password) VALUES (?, ?, ?)",
    ['Efe', 'efe@x.com', password_hash('secret', PASSWORD_BCRYPT)]
);

$id = $db->lastInsertId();   // yeni kaydın ID'si
```

### UPDATE

```php
$affected = $db->execute(
    "UPDATE users SET name = ? WHERE id = ?",
    ['Yeni Ad', 42]
);
// Etkilenen satır sayısı
```

### DELETE

```php
$db->execute("DELETE FROM users WHERE id = ?", [42]);
```

### Raw SQL

`exec()` parametre desteklemez — sadece DDL/DCL gibi sabit query'ler için:

```php
$db->exec("TRUNCATE TABLE traffic_logs");
```

---

## Transaction

Birden fazla query'yi atomik çalıştırmak için:

```php
$db = Database::getInstance();
$db->beginTransaction();

try {
    $db->execute("UPDATE accounts SET balance = balance - ? WHERE id = ?", [100, $from]);
    $db->execute("UPDATE accounts SET balance = balance + ? WHERE id = ?", [100, $to]);
    $db->commit();
} catch (\Throwable $e) {
    $db->rollBack();
    throw $e;
}
```

Helper:

```php
$db->transaction(function ($db) use ($from, $to) {
    $db->execute("UPDATE accounts SET balance = balance - 100 WHERE id = ?", [$from]);
    $db->execute("UPDATE accounts SET balance = balance + 100 WHERE id = ?", [$to]);
});
```

Closure exception fırlatırsa transaction otomatik rollback olur.

---

## Logging

Tüm sorgular **Debug Bar**'a otomatik loglanır:
- SQL metni
- Parameter binding'leri
- Süre (ms)

Production'da debug bar kapalı olduğu için bu log üretmez. Geliştirme sırasında alt çubuğa bakıp yavaş query'leri kolayca tespit edebilirsin.

---

**Sonraki:** [Query & PDO →](queries.md)
