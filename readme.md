# 🦅 IEF Framework

> **Version:** 1.0.0  
> **Author:** IEF Software  
> **License:** MIT

## 🌟 Hakkında

**IEF Framework**, modern PHP uygulamaları geliştirmek için tasarlanmış, **hafif (lightweight)**, **hızlı** ve **esnek** bir MVC (Model-View-Controller) çatısıdır. Gereksiz karmaşıklıktan uzak, anlaşılır yapısı ile hem öğrenmesi kolaydır hem de profesyonel projeler için güçlü bir temel sunar.

Framework; gelişmiş yönlendirme (routing), güvenli veritabanı işlemler (PDO & Active Record), otomatik CSRF koruması ve esnek middleware desteği ile gelir.

---

## 🚀 Özellikler

- **⚡ Yüksek Performans:** Gereksiz yüklerden arındırılmış çekirdek yapı.
- **🛣️ Gelişmiş Router:** RESTful rotalar, parametre desteği, gruplama ve middleware entegrasyonu.
- **💾 Active Record ORM:** Veritabanı işlemlerini nesne tabanlı ve güvenli bir şekilde yönetin (UUID desteği dahil).
- **🛡️ Güvenlik:** Dahili CSRF koruması, XSS filtreleme ve SQL Injection'a karşı PDO kullanımı.
- **🔌 CLI Aracı:** Proje yönetimi için `ief` konsol uygulaması.
- **📄 Şablon Motoru:** Yalın PHP tabanlı, performanslı view sistemi.
- **🔧 Helper Fonksiyonlar:** Geliştirme sürecini hızlandıran yardımcı araçlar.

---

## 🛠️ Kurulum

### Gereksinimler
- PHP 8.0 veya üzeri
- Composer
- SQLite veya MySQL/MariaDB

### Yeni Proje Oluşturma

1. **Projeyi Klonlayın:**
   ```bash
   git clone https://github.com/iefsoftware/ief-framework.git my-app
   cd my-app
   ```

2. **Bağımlılıkları Yükleyin:**
   ```bash
   composer install
   ```

3. **Yapılandırma:**
   `config/database.php` ve `config/app.php` dosyalarını projenize göre düzenleyin. Varsayılan olarak SQLite kullanır.

4. **Sunucuyu Başlatın:**
   ```bash
   ./ief serve
   ```
   Tarayıcınızda `http://localhost:8000` adresine gidin.

---

## 📖 Kullanım Kılavuzu

### 1. Dizin Yapısı
```
/
├── app/
│   ├── Controllers/   # İstekleri karşılayan sınıflar
│   ├── Core/          # Framework çekirdek dosyaları
│   ├── Models/        # Veritabanı modelleri
│   ├── Views/         # Arayüz dosyaları (HTML/PHP)
│   └── Helpers/       # Yardımcı fonksiyonlar
├── config/            # Ayar dosyaları (App, Database, Routes)
├── public/            # Web sunucusu kök dizini (assets vb.)
├── storage/           # Loglar ve veritabanı (SQLite)
├── vendor/            # Composer paketleri
├── ief                # CLI aracı
└── index.php          # Giriş noktası
```

### 2. Yönlendirme (Routing)

Rotalar `config/routes.php` dosyasında tanımlanır.

```php
use App\Core\Router;

// Basit GET isteği
Router::get('/', 'WelcomeController@index');

// Parametreli ve POST isteği
Router::post('/users/{id}/update', 'UserController@update');

// Callback fonksiyonu kullanımı
Router::get('/api/test', function() {
    return json_encode(['status' => 'ok']);
});
```

### 3. Controller

Controller sınıfları `app/Controllers` altında bulunur ve `App\Core\Controller` sınıfını miras almalıdır.

```php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;

class UserController extends Controller
{
    public function show($id)
    {
        // Model kullanımı
        $user = User::find($id);
        
        // View döndürme
        return $this->view('users/profile', [
            'user' => $user
        ]);
    }
}
```

### 4. Model (Veritabanı)

Modeller `app/Models` altında bulunur, `App\Core\Model` sınıfını miras alır ve veritabanı tablolarını temsil eder.

```php
namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected static string $table = 'users';
    protected static array $fillable = ['name', 'email'];
    protected static bool $useUuid = true; // UUID aktif
}
```

**Kullanım Örnekleri:**
```php
// Tüm kullanıcıları getir
$users = User::all();

// Yeni kullanıcı ekle
User::create([
    'name' => 'Ali Veli',
    'email' => 'ali@example.com'
]);

// Id ile bul
$user = User::find(1);

// Güncelle
User::update(1, ['name' => 'Ali Can']);
```

### 5. CLI Aracı

Proje kök dizinindeki `ief` komutu ile sunucuyu başlatabilir veya diğer işlemleri yapabilirsiniz.

```bash
./ief serve      # Sunucuyu 8000 portunda başlatır
./ief serve 8080 # Sunucuyu 8080 portunda başlatır
./ief help       # Yardım menüsü
```

---

## 🤝 Katkıda Bulunma

1. Fork yapın.
2. Branch oluşturun (`git checkout -b feature/yeni-ozellik`).
3. Commit atın (`git commit -m 'Yeni özellik eklendi'`).
4. Push yapın (`git push origin feature/yeni-ozellik`).
5. Pull Request açın.

---

## 🏗️ Örnek Uygulama (Görev Yöneticisi)

Proje içerisinde hazır gelen basit bir Görev Yönetimi (CRUD) uygulaması bulunmaktadır.

1. Veritabanını hazırlayın:
   ```bash
   php setup_db.php
   ```
2. Uygulamayı başlatın:
   ```bash
   ./ief serve
   ```
3. Tarayıcıda test edin:
   `http://localhost:8000/tasks`

---

**IEF Framework** &copy; 2024 - Tüm Hakları Saklıdır.
