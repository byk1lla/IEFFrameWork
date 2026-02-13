<?php

namespace App\Controllers;

use App\Core\Controller;

class ExampleController extends Controller
{
    public function index()
    {
        return $this->view('examples.index', [
            'title' => 'IEF Framework | Örnek Uygulamalar',
            'examples' => [
                [
                    'name' => 'Görev Yöneticisi (CRUD)',
                    'desc' => 'Temel ORM ve Routing özelliklerini kullanan tam kapsamlı görev listesi.',
                    'url' => '/tasks',
                    'icon' => '✅'
                ],
                [
                    'name' => 'Admin Dashboard',
                    'desc' => 'Layout ve Component sistemini kullanan premium yönetim paneli.',
                    'url' => '/admin',
                    'icon' => '📊'
                ],
                [
                    'name' => 'Interaktif Dokümantasyon',
                    'desc' => 'Framework özelliklerinin detaylı anlatımı ve kullanım kılavuzu.',
                    'url' => '/docs',
                    'icon' => '📚'
                ],
                [
                    'name' => 'Blog API (JSON)',
                    'desc' => 'JSON Resource ve API yönetimi için örnek bir backend yapısı.',
                    'url' => '/examples', // Placeholder
                    'icon' => '🌐'
                ]
            ]
        ]);
    }
}
