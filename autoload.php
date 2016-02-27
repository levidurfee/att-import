<?php
date_default_timezone_set('America/New_York');
define("DS", DIRECTORY_SEPARATOR);
define("ROOT", dirname(__FILE__));

function autoloadClass($class) {
    $class = str_replace("levidurfee\AttImport", "", $class);
    $classFile = ROOT . DS . 'classes' . $class . '.php';
    $interfaceFile = ROOT . DS . 'interfaces' . $class . 'Interface.php';
    if(is_readable($interfaceFile)) {
        require_once($interfaceFile);
    }
    if(is_readable($classFile)) {
        require_once($classFile);
    }
}
spl_autoload_register('autoloadClass');
