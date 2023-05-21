<?php

if (!defined('BOT_MIDRASH')) die('{"code":200}');

require_once(__DIR__ . DIRECTORY_SEPARATOR . 'config.php');

spl_autoload_register(function ($class) {
    $dirs = array(
        "objects",
        "commands",
        "."
    );

    foreach ($dirs as $dir) {
        if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $class . '.php')) {
            require_once(__DIR__ . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $class . '.php');
            return;
        }
    }

    // Else...
    helpers::logErrorToFile('Class "' . $class . '" Not Found!');
});
