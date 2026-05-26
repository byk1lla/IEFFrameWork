<?php
/**
 * WelcomeController — anasayfa.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;

class WelcomeController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->view('welcome', [
            'name'    => config('app.name', 'IEF Framework'),
            'version' => config('app.version', '2.0.0'),
        ]);
    }
}
