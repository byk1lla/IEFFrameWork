<?php

namespace App\Controllers;

use App\Core\Controller;

class ExampleController extends Controller
{
    public function index()
    {
        return $this->view('examples.index', [
            'title' => 'Elite Ecosystem | IEF Framework',
            'examples' => [
                [
                    'title' => 'Modern Blog System',
                    'description' => 'ORM tabanlı veri yönetimi ve Blade-lite inheritance demosudur.',
                    'url' => '/blog',
                    'icon' => '✍️'
                ],
                [
                    'title' => 'Ultimate Admin Panel',
                    'description' => 'Framework\'ün görsel yönetim arayüzü ve debug araçları merkezi.',
                    'url' => '/admin',
                    'icon' => '📊'
                ],
                [
                    'title' => 'Contact Hub',
                    'description' => 'MailService, Validation ve Logger servislerinin gerçek zamanlı entegrasyonu.',
                    'url' => '/contact',
                    'icon' => '✉️'
                ],
                [
                    'title' => 'Dokümantasyon',
                    'description' => 'Framework çekirdek özelliklerinin detaylı anlatımı.',
                    'url' => '/docs',
                    'icon' => '📚'
                ]
            ]
        ]);
    }
}
