<?php
function edm_autoload($class)
{
    $sinif = explode("\\", $class);
    $include_path = __DIR__ . "/";
    $dosya = $include_path . ($sinif[1] ?? $sinif[0]) . ".php";
    if (file_exists($dosya)) {
        include_once($dosya);
        return true;
    } else {
        return false;
    }
}
spl_autoload_register("edm_autoload");
?>