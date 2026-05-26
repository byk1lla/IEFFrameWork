# Validation

- [Giriş](#giris)
- [Hızlı Başlangıç](#hzl-balang)
- [Validator Sınıfı](#validator-snf)
- [Mevcut Kurallar](#mevcut-kurallar)
- [Özel Hata Mesajları](#zel-hata-mesajlar)
- [View'da Hata Gösterimi](#viewda-hata-gsterimi)
- [Old Input](#old-input)

---

## Giriş

IEF, gelen verileri doğrulamak için `App\Core\Validator` sınıfını sağlar. Validator, basit bir kural dizisi alır ve geçersiz alanlar için hata mesajları döndürür.

---

## Hızlı Başlangıç

```php
use App\Core\Validator;

public function submit(): Response
{
    $this->abortIfInvalidCsrf();

    $data = $this->request->all();
    $v = Validator::make($data, [
        'name'    => 'required|min:2|max:100',
        'email'   => 'required|email|max:191',
        'message' => 'required|min:10|max:2000',
    ]);

    if ($v->fails()) {
        $this->flash('errors', $v->errors());
        $this->flash('old', $data);
        return $this->redirect('/iletisim');
    }

    Message::create($v->validated());
    $this->flash('success', 'Mesaj iletildi.');
    return $this->redirect('/iletisim/tesekkurler');
}
```

---

## Validator Sınıfı

### Oluşturma

```php
$v = Validator::make(array $data, array $rules, array $messages = []);
```

### Sonuçları İnceleme

```php
$v->fails();        // bool — herhangi bir kural başarısız mı
$v->passes();       // bool — tüm kurallar başarılı mı
$v->errors();       // array<string,array> — alan başına hata listesi
$v->validated();    // array — sadece geçerli olan alanları döner
$v->first('email'); // string|null — alanın ilk hatası
```

---

## Mevcut Kurallar

| Kural | Açıklama |
|---|---|
| `required` | Boş olamaz |
| `nullable` | Boş olabilir |
| `email` | Geçerli e-posta adresi |
| `url` | Geçerli URL |
| `numeric` | Sadece sayı |
| `integer` | Tam sayı |
| `min:N` | String için min karakter, sayı için min değer |
| `max:N` | String için max karakter, sayı için max değer |
| `between:A,B` | Aralık |
| `in:foo,bar,baz` | Listedeki değerlerden biri |
| `not_in:foo,bar` | Listede olmayan |
| `regex:/pattern/` | Regex eşleşmesi |
| `same:field` | Başka alanla aynı (örn. password confirmation) |
| `different:field` | Başka alandan farklı |
| `confirmed` | `_confirmation` suffix'li alanla eşleşmeli (`password` + `password_confirmation`) |
| `unique:table,column` | DB'de o tabloda o sütun unique |
| `exists:table,column` | DB'de o tabloda kayıt var |
| `date` | Geçerli tarih |
| `date_format:Y-m-d` | Belirli format |
| `boolean` | true/false/0/1 |
| `array` | Dizi |
| `file` | Yüklenen dosya |
| `image` | jpg/png/gif/webp/svg uzantılı dosya |
| `mimes:jpg,pdf` | İzin verilen uzantılar |
| `max_size:5000` | Dosya boyutu KB |

Kuralları `|` ile birleştir:

```php
'avatar' => 'nullable|image|max_size:2048',
'password' => 'required|min:8|confirmed',
'role' => 'required|in:admin,editor,user',
```

---

## Özel Hata Mesajları

Üçüncü argüman olarak alana özel mesajlar ver:

```php
$v = Validator::make($data, [
    'email' => 'required|email|unique:users,email',
], [
    'email.required' => 'E-posta adresi zorunlu.',
    'email.email'    => 'Geçerli bir e-posta gir.',
    'email.unique'   => 'Bu e-posta zaten kayıtlı.',
]);
```

Genel mesajlar `resources/lang/{locale}.php` içinde `validation` anahtarı altında özelleştirilebilir.

---

## View'da Hata Gösterimi

Form sayfasında flash'tan oku:

```blade
@php $errors = flash('errors', []); @endphp

<form method="POST" action="/iletisim">
    @csrf

    <input type="email" name="email" value="{{ old('email') }}"
           class="{{ isset($errors['email']) ? 'border-red-500' : '' }}">

    @if(isset($errors['email']))
        <p class="text-red-600 text-sm mt-1">{{ $errors['email'][0] }}</p>
    @endif

    <button>Gönder</button>
</form>
```

---

## Old Input

Hata sonrası kullanıcı doldurduğu değerleri kaybetmesin diye:

```php
// Controller — başarısızsa
$this->flash('old', $this->request->all());
return $this->redirect('/iletisim');
```

```blade
<input name="name" value="{{ old('name') }}">
```

`old()` helper'ı session flash'tan o anahtarı okur, yoksa boş string döner.

---

**Sonraki:** [Hata Yönetimi →](errors.md)
