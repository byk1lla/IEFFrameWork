# Şifre Sıfırlama

- [Giriş](#giris)
- [Akış](#akis)
- [Route'lar](#routelar)
- [Token Güvenliği](#token-guvenligi)
- [Mail Şablonu](#mail-sablonu)

---

## Giriş

Token-tabanlı şifre sıfırlama hazır gelir. `password_resets` tablosu, geçici token + expire mekanizmasıyla.

---

## Akış

1. Kullanıcı `/sifre-sifirla` adresinden e-postasını girer
2. Sistemde kayıtlıysa: 64-karakter rastgele token üretilir, `password_resets`'e yazılır
3. Kullanıcıya mail gönderilir: `/sifre-sifirla/{token}` linki
4. Kullanıcı linke tıklar, yeni şifre formunu doldurur
5. Backend: token'ı doğrula (expire 60dk), şifreyi hash'le, kayıt et, token'ı sil
6. Kullanıcı login sayfasına yönlendirilir

Security: kullanıcı varsa-yoksa **aynı yanıt** verilir — e-posta enumeration saldırısına karşı.

---

## Route'lar

| Method | URL | İşlev |
|---|---|---|
| GET | `/sifre-sifirla` | Form (e-posta gir) |
| POST | `/sifre-sifirla` | Token üret + mail gönder |
| GET | `/sifre-sifirla/{token}` | Yeni şifre formu |
| POST | `/sifre-sifirla/{token}` | Şifreyi güncelle |

Controller: `App\Controllers\AuthController`.

---

## Token Güvenliği

- 64-karakter (32 byte hex)
- DB'de hash'lenmiş olarak saklanır (`hash('sha256', $token)`)
- 60 dakika geçerli
- Tek kullanımlık (success sonrası silinir)
- Aynı e-postaya ardışık talepler: eski token'lar silinir

---

## Mail Şablonu

`app/Views/emails/password-reset.php`:

```blade
<!doctype html>
<html><body>
    <h2>Şifrenizi sıfırlayın</h2>
    <p>Merhaba {{ $name }},</p>
    <p>Şifrenizi sıfırlamak için aşağıdaki linke tıklayın:</p>
    <p><a href="{{ $resetUrl }}">{{ $resetUrl }}</a></p>
    <p><small>Bu link 60 dakika içinde geçersiz olacaktır.</small></p>
</body></html>
```

`MailService::send(..., isHtml: true)` ile gönderilir.

---

**Sonraki:** [Sessions →](sessions.md)
