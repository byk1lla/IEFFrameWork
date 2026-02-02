<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;

class HizmetController extends Controller
{
    private $jsonFile;

    public function __construct()
    {
        parent::__construct();

        if (!Session::isLoggedIn()) {
            $this->redirect('/login');
            exit;
        }

        $this->jsonFile = __DIR__ . '/../../storage/json/services.json';

        // Ensure storage directory exists
        if (!is_dir(dirname($this->jsonFile))) {
            mkdir(dirname($this->jsonFile), 0777, true);
        }
    }

    public function index()
    {
        $services = $this->getServices();

        return $this->view('hizmet/index', [
            'title' => 'Hizmet ve Ürünler - E-Fatura Pro',
            'services' => $services,
            'layout' => 'app'
        ]);
    }

    public function create()
    {
        return $this->view('hizmet/create', [
            'title' => 'Yeni Hizmet Ekle - E-Fatura Pro',
            'layout' => 'app'
        ]);
    }

    public function store()
    {
        $name = $this->request->input('name');
        $price = (float) $this->request->input('price', 0);
        $tax = (int) $this->request->input('tax', 20); // Default 20%
        $unit = $this->request->input('unit', 'ADET');
        $isDefault = $this->request->input('is_default') === 'on';

        if (empty($name)) {
            $this->flash('error', 'Hizmet adı zorunludur.');
            return $this->redirect('/hizmet/yeni');
        }

        $services = $this->getServices();
        $id = uniqid();

        // If this is set to default, unset others //
        if ($isDefault) {
            foreach ($services as &$service) {
                $service['is_default'] = false;
            }
        }

        $services[$id] = [
            'id' => $id,
            'name' => $name,
            'price' => $price,
            'tax' => $tax,
            'unit' => $unit,
            'is_default' => $isDefault,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->saveServices($services);

        $this->flash('success', 'Hizmet başarıyla eklendi.');
        return $this->redirect('/hizmetler');
    }

    public function delete($id)
    {
        $services = $this->getServices();

        if (isset($services[$id])) {
            unset($services[$id]);
            $this->saveServices($services);
            $this->flash('success', 'Hizmet silindi.');
        } else {
            $this->flash('error', 'Hizmet bulunamadı.');
        }

        return $this->redirect('/hizmetler');
    }

    public function setDefault($id)
    {
        $services = $this->getServices();

        if (isset($services[$id])) {
            // Reset all
            foreach ($services as &$service) {
                $service['is_default'] = false;
            }
            // Set new default
            $services[$id]['is_default'] = true;

            $this->saveServices($services);
            $this->flash('success', 'Varsayılan hizmet güncellendi.');
        }

        return $this->redirect('/hizmetler');
    }

    private function getServices()
    {
        if (file_exists($this->jsonFile)) {
            return json_decode(file_get_contents($this->jsonFile), true) ?: [];
        }
        return [];
    }

    private function saveServices($services)
    {
        file_put_contents($this->jsonFile, json_encode($services, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
