# Mail

- [Giriş](#giris)
- [Konfigürasyon](#konfigurasyon)
- [Driver'lar](#driverlar)
    - [log](#log)
    - [mail](#mail)
    - [smtp](#smtp)
- [Mail Gönderme](#mail-gonderme)
- [HTML Mail](#html-mail)
- [Attachment](#attachment)
- [Admin Bildirimler](#admin-bildirimler)

---

## Giriş

IEF, popüler [PHPMailer](https://github.com/PHPMailer/PHPMailer) kütüphanesi üzerine inşa edilmiş temiz, basit bir mail API'si sağlar. Mail göndermek için `App\Services\MailService` kullanılır.

---

## Konfigürasyon

İki yerden ayarlanır:

1. **Static defaults** — `config/mail.php`
2. **Override edilebilir** — Admin > Ayarlar > Mail (DB'den okunur, statik üzerine yazılır)

```php
// config/mail.php
return [
    'driver' => 'smtp',  // log | mail | smtp
    'from'   => [
        'address' => 'noreply@example.com',
        'name'    => 'Site Bildirim',
    ],
    'smtp' => [
        'host'       => 'smtp.example.com',
        'port'       => 587,
        'username'   => 'user',
        'password'   => 'gizli',
        'encryption' => 'tls',
    ],
];
```

---

## Driver'lar

### log

Mail göndermez — `storage/logs/mail-YYYY-MM-DD.log` dosyasına yazar. **Dev için ideal**.

```
[2026-05-25 14:30:00]
To: efe@x.com
From: noreply@example.com
Subject: Yeni mesaj
Body:
Merhaba, yeni bir iletişim mesajı geldi...
---
```

### mail

PHP'nin yerleşik `mail()` fonksiyonunu kullanır. Shared hosting'lerde çalışır ama spam filtrelere takılma riski yüksek.

### smtp

Gerçek SMTP sunucusu üzerinden gönderim. **Production önerilen**.

Popüler servisler:
- **Gmail SMTP** — `smtp.gmail.com:587` (App Password ile)
- **SendGrid** — `smtp.sendgrid.net:587`
- **Mailgun** — `smtp.mailgun.org:587`
- **Amazon SES** — `email-smtp.us-east-1.amazonaws.com:587`

---

## Mail Gönderme

```php
use App\Services\MailService;

(new MailService())->send(
    to:      'kullanici@example.com',
    subject: 'Hoş Geldin',
    body:    'Aramıza katıldığın için teşekkürler!',
);
```

Birden fazla alıcı:

```php
(new MailService())->send(
    to:      ['user1@x.com', 'user2@x.com'],
    subject: 'Bildirim',
    body:    'Yeni güncelleme...',
);
```

---

## HTML Mail

`isHtml: true` flag'i ile:

```php
(new MailService())->send(
    to:      $user->email,
    subject: 'Şifre Sıfırlama',
    body:    '<h1>Selam</h1><p>Sıfırlama linkin: <a href="' . $url . '">tıkla</a></p>',
    isHtml:  true,
);
```

> Mail HTML'i için her zaman düz metin alternatifi ekle (`altBody` parametresi) — spam filtreler buna bakar.

---

## Attachment

```php
(new MailService())->send(
    to:          $email,
    subject:     'Faturanız',
    body:        'Ekteki PDF\'i bulabilirsiniz.',
    attachments: [
        '/path/to/invoice-001.pdf',
        '/path/to/contract.pdf' => 'Sözleşme.pdf',  // custom isim
    ],
);
```

---

## Admin Bildirimler

İletişim formu/randevu/yorum gibi olaylarda admin'e bilgi ver. Hedef e-posta admin panelinden tanımlı:

**Admin > Ayarlar > Mail > Admin Inbox**

```php
$adminEmail = Setting::get('mail.admin_inbox');
if ($adminEmail) {
    (new MailService())->send(
        to:      $adminEmail,
        subject: 'Yeni iletişim mesajı',
        body:    "İsim: {$name}\nE-posta: {$email}\nMesaj: {$message}",
    );
}
```

---

**Sonraki:** [İletişim Formu →](contact.md)
