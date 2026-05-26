# Query Builder & PDO

- [Giriş](#giris)
- [Sorgu Çalıştırma](#sorgu-altrma)
- [Where Koşulları](#where-koullar)
- [Join'ler](#joinler)
- [Sıralama, Limit, Offset](#siralama-limit-offset)
- [Insert / Update / Delete](#insert-update-delete)
- [Aggregate (count, sum, ...)](#aggregate)
- [Ham PDO](#ham-pdo)

---

## Giriş

IEF, Eloquent benzeri zincirleme bir query builder sunmaz; bunun yerine **Model** (Obsidian ORM) ve **`Database`** facade'i üzerinden iki yol sağlar. Model genelde tek tablo işlemleri için yeterlidir; daha kompleks query'ler için doğrudan `Database` kullanılır.

> Detay: [Modeller →](models.md) · [Database →](database.md)

---

## Sorgu Çalıştırma

```php
use App\Core\Database;
$db = Database::getInstance();

// SELECT
$rows = $db->fetchAll("SELECT * FROM posts WHERE published = ?", [1]);
$row  = $db->fetch("SELECT * FROM posts WHERE id = ?", [42]);
$cnt  = $db->fetchColumn("SELECT COUNT(*) FROM posts");
```

---

## Where Koşulları

Model üzerinden:

```php
Post::where('status', 'published')->get();
Post::where('created_at', '>=', '2026-01-01')->get();
Post::where('title', 'like', '%framework%')->get();
Post::whereIn('id', [1, 2, 3])->get();
Post::whereNull('deleted_at')->get();
```

Birden fazla:

```php
Post::where('published', 1)
    ->where('user_id', $userId)
    ->where('created_at', '>', $since)
    ->get();
```

---

## Join'ler

Query builder join yok; ham SQL kullan:

```php
$rows = $db->fetchAll("
    SELECT p.*, u.name AS author
    FROM posts p
    JOIN users u ON u.id = p.user_id
    WHERE p.published = ?
    ORDER BY p.created_at DESC
    LIMIT 20
", [1]);
```

---

## Sıralama, Limit, Offset

```php
Post::orderBy('created_at', 'desc')
    ->limit(10)
    ->offset(20)
    ->get();
```

Pagination örneği:

```php
public function index(): Response
{
    $page    = (int) $this->request->query('p', 1);
    $perPage = 20;
    $offset  = ($page - 1) * $perPage;

    $posts = Post::where('published', 1)
                 ->orderBy('created_at', 'desc')
                 ->limit($perPage)
                 ->offset($offset)
                 ->get();

    $total = Post::where('published', 1)->count();
    $pages = (int) ceil($total / $perPage);

    return $this->view('blog.index', compact('posts', 'page', 'pages'));
}
```

---

## Insert / Update / Delete

```php
// INSERT
$db->execute("INSERT INTO posts (title, slug) VALUES (?, ?)", ['T', 's']);
$newId = $db->lastInsertId();

// UPDATE
$affected = $db->execute("UPDATE posts SET title = ? WHERE id = ?", ['Yeni', 42]);

// DELETE
$db->execute("DELETE FROM posts WHERE id = ?", [42]);
```

---

## Aggregate

```php
$count = Post::count();
$count = Post::where('published', 1)->count();

$max   = $db->fetchColumn("SELECT MAX(created_at) FROM posts");
$sum   = $db->fetchColumn("SELECT SUM(amount) FROM orders WHERE paid = 1");
$avg   = $db->fetchColumn("SELECT AVG(score) FROM ratings");
```

---

## Ham PDO

Tam kontrol için:

```php
$pdo  = Database::getInstance()->pdo();
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([42]);
$post = $stmt->fetch(\PDO::FETCH_ASSOC);
```

> Performans-kritik veya kompleks query'lerde PDO'ya gerilemek **tamamen meşru**dur — framework bunu engellemez.

---

**Sonraki:** [Migration'lar →](migrations.md)
