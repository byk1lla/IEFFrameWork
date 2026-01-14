<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════════
 * IEF FRAMEWORK - Single Entry Point
 * ═══════════════════════════════════════════════════════════════════════════════
 * 
 * @package    IEF Framework
 * @version    1.0.0
 * @author     IEF Software
 */

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', '1'); // Set to 0 in production

// Timezone
date_default_timezone_set('Europe/Istanbul');

// Encoding
mb_internal_encoding('UTF-8');

// Constants
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('ASSETS_PATH', ROOT_PATH . '/assets');
define('VIEW_PATH', APP_PATH . '/Views');

// Composer Autoload
require_once ROOT_PATH . '/vendor/autoload.php';

// Helpers
if (file_exists(APP_PATH . '/Helpers/helpers.php')) {
    require_once APP_PATH . '/Helpers/helpers.php';
}

use App\Core\Session;
use App\Core\App;
use App\Core\ExceptionHandler;

// Start Session
Session::start();

// Set Debug Mode (should be based on ENV in real app)
ExceptionHandler::setDebug(true);

try {
    $app = new App();
    $app->run();
} catch (Throwable $e) {
    ExceptionHandler::handle($e);
}
