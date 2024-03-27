<?php

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', realpath(dirname(__FILE__) . DS . '..' . DS . '..') . DS);
define('APP', ROOT . 'app' . DS);
define('CORE', APP . 'core' . DS);
define('MODEL', APP .'model'. DS);
define('VIEW', APP .'view'. DS);
define('CONTROLLER', APP .'controller'. DS);
define('DATA', APP .'data'. DS);

$modules = [ROOT, APP, CORE, MODEL, VIEW, CONTROLLER, DATA];
set_include_path(get_include_path() . PATH_SEPARATOR . implode(PATH_SEPARATOR, $modules));



spl_autoload_register(function ($name) {
    if (is_file(CORE . $name . '.php')) {
        require_once(CORE . $name . '.php');
    }
});
spl_autoload_register(function ($name) {
    if (is_file(CONTROLLER . $name . '.php')) {
        require_once(CONTROLLER . $name . '.php');
    }
});

spl_autoload_register(function ($name) {
    if (is_file(MODEL . $name . '.php')) {
        require_once(MODEL . $name . '.php');
    }
});

?>