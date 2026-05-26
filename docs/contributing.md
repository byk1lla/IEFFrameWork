# Katkıda Bulunma

- [Geri Bildirim](#geri-bildirim)
- [Bug Bildirimi](#bug-bildirimi)
- [Pull Request](#pull-request)
- [Kodlama Standartları](#kodlama-standartlari)
- [Dokümantasyon](#dokumantasyon)

---

## Geri Bildirim

Önerileri, bug raporlarını, sorularını [GitHub Issues](https://github.com/byk1lla/IEFFrameWork/issues) üzerinden iletebilirsin.

E-posta: [info@iefsoftware.tr](mailto:info@iefsoftware.tr)

---

## Bug Bildirimi

Bug raporlaman için lütfen şunları içer:

1. **IEF versiyonu** (`config/app.php`'deki `version`)
2. **PHP versiyonu** (`php -v`)
3. **Beklenen davranış**
4. **Gerçekleşen davranış**
5. **Reprodüksiyon adımları** (en az 1-2-3-...)
6. **Error log** (`storage/logs/app-YYYY-MM-DD.log`)
7. (Varsa) ekran görüntüsü

---

## Pull Request

1. Repo'yu fork'la
2. Yeni branch: `feat/yeni-ozellik` veya `fix/issue-42`
3. Değişiklikleri yap, **TR commit mesajı**:
   ```
   feat: Site editör'e font picker ekle
   fix: CSRF token süresi yenilenmiyor
   docs: routing.md'de örnek düzeltildi
   ```
4. Push + PR aç
5. PR açıklamasında: ne değişti, neden, nasıl test ettin

> Küçük PR'lar daha hızlı merge olur. Tek bir konuya odaklı tut.

---

## Kodlama Standartları

- **PHP:** PSR-12
- **Naming:** Sınıflar `PascalCase`, method'lar `camelCase`, sabitler `SCREAMING_SNAKE`
- **Yorumlar:** Türkçe (proje Türkçe)
- **Değişken/fonksiyon isimleri:** İngilizce
- **Tab/space:** 4 space
- **Tip işaretleri:** Mümkün olduğunca `string`, `int`, `array`, `?Type`, `Type|null`

```php
public function show(int $id): ?Response
{
    $post = Post::find($id);
    if (!$post) {
        return null;
    }
    return $this->view('blog.show', ['post' => $post]);
}
```

---

## Dokümantasyon

`docs/*.md` dosyaları `/docs` sayfasında render edilir. Markdown standart syntax + bazı Parsedown ek özelliği.

- Her dosya en üstte: TOC linkleri
- Başlıklar `##`, `###` ile, h1 sadece bir tane (sayfa başlığı)
- Kod blokları için 3-tırnak + dil: ` ```php `
- Bağlantılar: `[link](other-doc.md)` — controller otomatik `/docs/...`'e çevirir
- > blockquote'lar **tip** veya **uyarı** için

Yeni dokümantasyon eklemek:
1. `docs/yeni-konu.md` oluştur
2. `app/Controllers/DocsController.php`'de `$nav` dizisine ekle

---

**İletişim:** [iefsoftware.tr](https://iefsoftware.tr)
