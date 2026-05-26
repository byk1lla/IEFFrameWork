# Şifreleme

- [Giriş](#giris)
- [Kullanım](#kullanim)
- [Şifre Hashing](#sifre-hashing)
- [HMAC](#hmac)

---

## Giriş

IEF dedicated bir encryption servisi sağlamaz; PHP'nin built-in fonksiyonları (`openssl_*`, `hash_*`, `password_*`) yeterlidir.

---

## Kullanım

### Symmetric Encryption (AES-256-GCM)

```php
function encrypt(string $plain, string $key): string {
    $iv = random_bytes(12);
    $cipher = openssl_encrypt($plain, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
    return base64_encode($iv . $tag . $cipher);
}

function decrypt(string $encoded, string $key): string {
    $data   = base64_decode($encoded);
    $iv     = substr($data, 0, 12);
    $tag    = substr($data, 12, 16);
    $cipher = substr($data, 28);
    return openssl_decrypt($cipher, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
}
```

`$key`: `config/app.php`'deki `key` alanı veya `random_bytes(32)` ile üretilmiş 32-byte string.

---

## Şifre Hashing

`password_hash()` + `password_verify()` — PHP built-in, bcrypt default:

```php
$hash = password_hash($plain, PASSWORD_BCRYPT, ['cost' => 12]);

if (password_verify($plain, $hash)) {
    // ✓
}
```

> Cost = 12 production için iyi denge. Donanımına göre `password_needs_rehash()` ile periyodik upgrade et.

---

## HMAC

API webhook signature doğrulama gibi yerlerde:

```php
$expected = hash_hmac('sha256', $payload, $secret);
if (hash_equals($expected, $providedSignature)) {
    // ✓
}
```

`hash_equals` timing-safe karşılaştırma yapar — `===` yerine her zaman bunu kullan.

---

**Sonraki:** [Rate Limiting →](rate-limiting.md)
