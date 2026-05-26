# Kullanıcı Yönetimi

- [Giriş](#giris)
- [Admin Sayfaları](#admin-sayfalari)
- [Kullanıcı Oluşturma](#kullanici-olusturma)
- [Rol Atama](#rol-atama)
- [Kullanıcı Silme](#kullanici-silme)
- [CLI ile Oluşturma](#cli-ile-olusturma)

---

## Giriş

Admin > Kullanıcılar — `/admin/users` — sistem kullanıcılarını yönetir. Sadece `superadmin` rolü tam erişime sahiptir; `admin` görüntüleyebilir ama düşük rol kullanıcıları yönetebilir.

---

## Admin Sayfaları

| URL | İşlev |
|---|---|
| `/admin/users` | Liste (DataTable) |
| `/admin/users/create` | Yeni kullanıcı |
| `/admin/users` (POST) | Kaydet |
| `/admin/users/{id}/edit` | Düzenle |
| `/admin/users/{id}/update` (POST) | Güncelle |
| `/admin/users/{id}/delete` (POST) | Sil |

---

## Kullanıcı Oluşturma

Form alanları:
- İsim
- E-posta (unique)
- Rol (superadmin / admin / editor / user)
- Şifre (min 8 karakter, bcrypt hash)
- (opsiyonel) Avatar upload

Submit → CSRF doğrulanır → validation → `User::create()` → flash success → liste.

---

## Rol Atama

Roller `users.role` sütununda string olarak tutulur:

| Role | Yetki |
|---|---|
| `superadmin` | Her şey + sistem ayarları |
| `admin` | Panel + içerik |
| `editor` | Sadece içerik (blog, editör) |
| `user` | Panel yok, sadece public site |

> RBAC detayı: [Authorization →](authorization.md)

Bir kullanıcının rolünü değiştirmek için: edit form'undaki dropdown'dan seç → kaydet.

> **Güvenlik:** Bir admin **kendinden daha yüksek rol veremez**. Kontrol controller'da yapılır.

---

## Kullanıcı Silme

DataTable satırındaki sil butonu SweetAlert ile onay sorar. Onaylandığında DELETE çalışır.

> **Soft delete yok** — silinen kullanıcı kalıcıdır. Korumak istiyorsan migration ile `deleted_at` timestamp sütunu ekle ve modelinde filter uygula.

---

## CLI ile Oluşturma

İlk admin için (DB boş iken) tek yol — interaktif:

```bash
./ief user:create
```

```
İsim: Efe
E-posta: efe@example.com
Rol (superadmin|admin|editor|user) [superadmin]:
Şifre: ********
Şifre (tekrar): ********
✓ Kullanıcı oluşturuldu: efe@example.com (superadmin).
```

Var olan kullanıcıyı güncellemek için aynı komutu aynı e-postayla çalıştır → "güncellensin mi?" sorar.

> Detay: [CLI →](cli.md#kullanici-yonetimi)

---

**Sonraki:** [AI / Groq →](ai-groq.md)
