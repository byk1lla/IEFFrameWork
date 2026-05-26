# Wappalyzer / BuiltWith Fingerprint Submission

Bu döküman, IEF Framework'ün teknoloji parmak izi araçları tarafından otomatik
tanınması için yapılması gereken submission adımlarını anlatır.

## Halihazırda Eklenen İşaretler (Repo İçi)

Bu işaretler **kaldırılmamalı** — tanıma için zorunlu:

1. **HTTP Response Headers** ([app/Core/App.php](../app/Core/App.php) constructor):
   - `X-Powered-By: IEF-Framework/{version}`
   - `X-Generator: IEF Framework {version}`

2. **HTML Meta Tags** ([app/Views/layouts/app.php](../app/Views/layouts/app.php) ve
   [app/Views/layouts/admin.php](../app/Views/layouts/admin.php)):
   - `<meta name="generator" content="IEF Framework {version}">`
   - `<meta name="framework" content="IEF Framework">`

Versiyon değeri `config/app.php` → `app.version` üzerinden okunur.

## Wappalyzer'a Submission

Orijinal Wappalyzer projesi (`AliasIO/wappalyzer`) artık maintain edilmiyor.
Aktif fork: **enthec/webappanalyzer** (Chrome/Firefox extension'ı buradan).

### Adımlar

1. Fork: https://github.com/enthec/webappanalyzer
2. Dosya yolu: `src/technologies/i.json` (yeni teknolojiler alfabetik dosyalara
   eklenir — "I" harfi için).
3. Aşağıdaki JSON bloğunu o dosyaya ekle (mevcut içeriğin sonuna, alfabetik
   sırada).
4. İkon: `src/images/icons/IEF.svg` olarak SVG logo ekle (~32x32, basit).
5. PR aç, başlık: `Add: IEF Framework`.

### JSON Bloğu

[wappalyzer-fingerprint.json](wappalyzer-fingerprint.json) dosyasındaki blok
kullanılacak. Kısaca:

```json
"IEF Framework": {
  "description": "Modern, lightweight PHP framework by IEF Software.",
  "cats": [18],
  "website": "https://iefsoftware.tr",
  "icon": "IEF.svg",
  "headers": {
    "X-Powered-By": "IEF-Framework(?:/([\\d.]+))?\\;version:\\1",
    "X-Generator": "IEF Framework(?: ([\\d.]+))?\\;version:\\1"
  },
  "meta": {
    "generator": "IEF Framework(?: ([\\d.]+))?\\;version:\\1",
    "framework": "^IEF Framework$"
  },
  "implies": ["PHP"],
  "oss": true
}
```

Kategori `18` = "Web frameworks".

### Test Etme

PR onaylanmadan önce extension'ı local olarak test edebilirsin:

```bash
git clone https://github.com/enthec/webappanalyzer
cd webappanalyzer
yarn install
yarn validate    # JSON schema kontrolü
yarn build
# build/ klasörünü Chrome → Extensions → Load Unpacked ile yükle
```

Sonra IEF Framework çalışan bir site aç → Wappalyzer ikonunda "IEF Framework
2.0.0" görünmeli.

## BuiltWith

BuiltWith'in açık submission API'si yok. Yeterli sayıda site IEF Framework
header/meta'sını yayınlamaya başladığında otomatik crawl ile yakalıyor
(genelde 50+ canlı site). Bu nedenle:

- iefsoftware.tr (ana site)
- Müşteri projelerinden public olanlar
- to-do.iefsoftware.tr

bu fingerprint'i yayınladığı sürece BuiltWith zamanla yakalar.

## İkon (SVG)

PR için gereken IEF logosu — basit, monokrom, kare alanda. `IEF.svg` adıyla
hazırlanıp PR'a eklenmeli. Halihazırda landing'de logo varsa ([public/img/](../public/img/))
oradan optimize edilmiş bir versiyon kullanılabilir.
