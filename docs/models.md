# Modeller (ORM)

- [Giriş](#giris)
- [Model Oluşturma](#model-olusturma)
- [Konvansiyonlar](#konvansiyonlar)
- [Veri Alma](#veri-alma)
    - [find / first / all](#find-first-all)
    - [Where](#where)
    - [Ordering & Limit](#ordering-limit)
- [Veri Yazma](#veri-yazma)
    - [create](#create)
    - [save](#save)
    - [update](#update)
- [Veri Silme](#veri-silme)
- [İlişkiler](#iliskiler)
- [Accessor & Mutator](#accessor-mutator)

---

## Giriş

IEF, veritabanıyla çalışmayı keyifli hale getiren **Obsidian ORM**'i içerir. Obsidian, basit, hafif ve Active Record paterniyle çalışan bir ORM'dir. Her veritabanı tablosu için karşılık gelen bir "Model" sınıfı tanımlanır.

Modeller, kayıtları sorgulayabilmenin yanı sıra tabloya yeni kayıt eklemek için de kullanılır.

---

## Model Oluşturma

CLI ile:

```bash
./ief make:model Post
```

Bu, `app/Models/Post.php` dosyasını üretir:

```php
<?php

namespace App\Models;

use App\Core\Model;

class Post extends Model
{
    protected static string $table = 'posts';
    protected static string $primaryKey = 'id';
}
```

---

## Konvansiyonlar

- **Tablo adı:** Snake-case + çoğul (`Post` → `posts`)
- **Primary key:** `id`
- **Auto-increment:** evet
- **Timestamps:** `created_at` ve `updated_at` otomatik yönetilir (`timestamps()` Blueprint helper'ı ile eklendiyse)

Konvansiyonu override etmek için model'de:

```php
class Post extends Model
{
    protected static string $table = 'blog_posts';       // farklı tablo
    protected static string $primaryKey = 'uuid';        // farklı PK
    protected static bool $timestamps = false;            // timestamps yok
}
```

---

## Veri Alma

### find / first / all

```php
use App\Models\Post;

// ID'ye göre tek kayıt (Post veya null)
$post = Post::find(42);

// İlk kayıt
$post = Post::first();

// Tüm kayıtlar (array<Post>)
$posts = Post::all();
```

### Where

```php
$published = Post::where('published', 1)->get();
$recent    = Post::where('created_at', '>=', '2026-01-01')->get();
$bySlug    = Post::where('slug', $slug)->first();

// Birden fazla koşul
$query = Post::where('published', 1)
             ->where('user_id', $userId)
             ->orderBy('created_at', 'desc')
             ->limit(10);
$rows = $query->get();
```

`where()` operatörleri: `=`, `!=`, `<`, `<=`, `>`, `>=`, `like`, `in`.

### Ordering & Limit

```php
Post::orderBy('created_at', 'desc')->limit(20)->get();
Post::orderBy('title')->offset(40)->limit(20)->get();   // pagination

// Sayım
$count = Post::where('published', 1)->count();
```

---

## Veri Yazma

### create

Mass-assignment:

```php
$post = Post::create([
    'title'   => 'İlk Yazım',
    'slug'    => 'ilk-yazim',
    'body'    => '<p>İçerik...</p>',
    'user_id' => Auth::id(),
]);

echo $post->id;        // yeni ID
```

### save

```php
$post = new Post();
$post->title = 'İlk Yazım';
$post->slug  = 'ilk-yazim';
$post->save();
```

### update

```php
$post = Post::find(42);
$post->title = 'Düzeltildi';
$post->save();

// Veya tek seferde:
$post->update(['title' => 'Düzeltildi', 'published' => 1]);
```

Mass update:

```php
Post::where('user_id', 1)->update(['published' => 0]);
```

---

## Veri Silme

```php
$post = Post::find(42);
$post->delete();

// Tek seferde:
Post::destroy(42);
Post::destroy([42, 43, 44]);

// Where ile:
Post::where('published', 0)
    ->where('created_at', '<', '2025-01-01')
    ->delete();
```

---

## İlişkiler

> Şu an Obsidian, manuel ilişki yöntemini kullanır — eager loading sözdizimi yoktur. Custom method ile tanımla:

```php
class Post extends Model
{
    public function user(): ?User
    {
        return User::find($this->user_id);
    }

    public function comments(): array
    {
        return Comment::where('post_id', $this->id)->get();
    }
}
```

Kullanım:

```php
$post = Post::find(42);
$author = $post->user();
$comments = $post->comments();
```

> N+1 sorunundan kaçınmak için liste sayfalarında manuel join kullan veya `whereIn` ile toplu çek.

---

## Accessor & Mutator

Otomatik dönüşüm henüz yok — magic getter override ile yapabilirsin:

```php
class User extends Model
{
    public function __get($name)
    {
        if ($name === 'full_name') {
            return $this->first_name . ' ' . $this->last_name;
        }
        return parent::__get($name);
    }
}
```

---

**Sonraki:** [Seeder'lar →](seeders.md)
