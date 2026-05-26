<?php
/**
 * AdminController — auth korumalı admin dashboard (stub).
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;

class AdminController extends Controller
{
    public function index(): Response
    {
        return $this->view('admin.index');
    }
}
