# Cache

- [Giriş](#giris)
- [Dosya Cache](#dosya-cache)
- [Cache Helper](#cache-helper)
- [Cache Temizleme](#cache-temizleme)
- [İleride: Redis](#ileride-redis)

---

## Giriş

IEF'in built-in cache layer'ı **minimal**dir — `storage/cache/` altına basit JSON dosyaları yazar. Yüksek trafik için Redis veya APCu öneriliyor (manuel entegrasyon).

---

## Dosya Cache

`storage/cache/` dizinine yazılır. Her cache entry bir dosyadır:

```
storage/cache/
├── abc123def456.json   # key MD5
├── ...
```

İçerik:

```json
{"expires": 1716736800, "value": "..."}
```

---

## Cache Helper

> **Not:** `Cache` sınıfı henüz built-in değil — basit bir wrapper kendin yazabilirsin:

```php
class Cache
{
    protected static string $dir = STORAGE_PATH . '/cache';

    public static function get(string $key, $default = null)
    {
        $file = self::path($key);
        if (!is_file($file)) return $default;

        $data = json_decode(file_get_contents($file), true);
        if ($data['expires'] < time()) {
            @unlink($file);
            return $default;
        }
        return $data['value'];
    }

    public static function put(string $key, $value, int $seconds): void
    {
        if (!is_dir(self::$dir)) @mkdir(self::$dir, 0775, true);
        file_put_contents(self::path($key), json_encode([
            'expires' => time() + $seconds,
            'value'   => $value,
        ]));
    }

    public static function forget(string $key): void
    {
        @unlink(self::path($key));
    }

    public static function remember(string $key, int $seconds, callable $callback)
    {
        $val = self::get($key);
        if ($val !== null) return $val;
        $val = $callback();
        self::put($key, $val, $seconds);
        return $val;
    }

    protected static function path(string $key): string
    {
        return self::$dir . '/' . md5($key) . '.json';
    }
}
```

Kullanım:

```php
$posts = Cache::remember('popular-posts', 600, function () {
    return Post::orderBy('views', 'desc')->limit(10)->get();
});
```

---

## Cache Temizleme

CLI:

```bash
./ief cache:clear
```

`storage/cache/` dizinindeki tüm dosyaları siler.

---

## İleride: Redis

Yüksek trafikli site için Redis öner:

```php
$redis = new \Redis();
$redis->connect('127.0.0.1', 6379);
$redis->setex('key', 600, json_encode($value));
$value = json_decode($redis->get('key'), true);
```

Predis veya PhpRedis kullanılabilir. Resmi Cache layer'ı gelecek sürümlerde gelecek.

---

**Sonraki:** [Mimari →](architecture.md)
