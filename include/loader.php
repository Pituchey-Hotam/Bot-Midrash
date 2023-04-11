<?php

if (!defined('BOT_MIDRASH')) die('{"code":200}');

require_once(__DIR__ . DIRECTORY_SEPARATOR . 'config.php');

spl_autoload_register(function ($class) {
    if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'objects' . DIRECTORY_SEPARATOR . $class . '.php')) {
        require_once(__DIR__ . DIRECTORY_SEPARATOR . 'objects' . DIRECTORY_SEPARATOR . $class . '.php');
    }
    else if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'commands' . DIRECTORY_SEPARATOR . $class . '.php')) {
        require_once(__DIR__ . DIRECTORY_SEPARATOR . 'commands' . DIRECTORY_SEPARATOR . $class . '.php');
    }
    else if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . $class . '.php')) {
        require_once(__DIR__ . DIRECTORY_SEPARATOR . $class . '.php');
    }
    else {
        helpers::logErrorToFile('Class "' . $class . '" Not Found!');
    }
});
