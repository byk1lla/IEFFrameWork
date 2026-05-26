# Sessions

- [Giriş](#giris)
- [Konfigürasyon](#konfigurasyon)
- [Session ile Etkileşim](#session-ile-etkileim)
    - [Veri Alma](#veri-alma)
    - [Veri Yazma](#veri-yazma)
    - [Veri Silme](#veri-silme)
    - [Session'ı Yenileme](#sessioni-yenileme)
- [Flash Data](#flash-data)
- [Session Driver](#session-driver)

---

## Giriş

HTTP-driven uygulamalar stateless olduğundan, session'lar birden çok istek arasında kullanıcı bilgisi saklamak için bir yol sağlar. IEF, `App\Core\Session` sınıfı üzerinden basit ve birleşik bir API sağlar.

---

## Konfigürasyon

Session'lar varsayılan olarak dosya tabanlıdır ve `sessions/` dizinine yazılır. Bu klasörün web sunucusu tarafından **yazılabilir** olması gerekir:

```bash
chmod 775 sessions
chown www-data:www-data sessions
```

Ömür süresi `config/app.php`'de değil — admin panelinden değiştirilir:

**Admin > Ayarlar > Güvenlik > Oturum Ömrü (dakika)** — varsayılan 120 dakika.

---

## Session ile Etkileşim

### Veri Alma

```php
use App\Core\Session;

$value = Session::get('key');                 // null | mixed
$value = Session::get('key', 'default');      // varsayılan değer

if (Session::has('key')) {
    // ...
}
```

### Veri Yazma

```php
Session::set('user_id', 42);
Session::set('preferences', ['theme' => 'dark']);
```

### Veri Silme

```php
Session::remove('key');
Session::clear();   // tüm session
```

### Session'ı Yenileme

Session fixation saldırılarına karşı, login/logout sonrası session ID'yi yenile:

```php
Session::regenerate();
```

Bu, oturum verisini koruyarak yeni bir session ID üretir. `Auth::login()` bunu otomatik yapar.

---

## Flash Data

Flash data — bir sonraki istekte mevcut olan, ardından otomatik silinen veri. İdeal kullanımı: success/error mesajları.

### Yazma

```php
$this->flash('success', 'Mesaj iletildi.');
$this->flash('error', 'Geçersiz e-posta.');
```

Helper:

```php
flash('success', 'Tamam');     // yaz
```

### Okuma

```blade
@if(flash('success'))
    <div class="alert alert-success">{{ flash('success') }}</div>
@endif

@if(flash('error'))
    <div class="alert alert-error">{{ flash('error') }}</div>
@endif
```

`flash($key)` mesajı okuyup session'dan **siler** — bir sonraki istekte tekrar görünmez.

### Old Input

Form validation hatası sonrası kullanıcı input'unu korumak için:

```php
$this->flash('old', $this->request->all());
return $this->redirect('/iletisim');
```

```blade
<input name="email" value="{{ old('email') }}">
```

---

## Session Driver

Varsayılan: **dosya tabanlı** (`sessions/sess_<id>`). Yüksek trafikli production'da:

- **Redis:** PHP'nin `session.save_handler=redis` ini `php.ini`'de set et — IEF kod değişikliği gerektirmez
- **DB:** Henüz built-in yok; gelecekte gelebilir

> Yatayda ölçeklenen sunucular için Redis veya sticky session zorunludur — dosya tabanlı session'lar tek sunucuya bağlıdır.

---

**Sonraki:** [Helper Fonksiyonlar →](helpers.md)
