<?php

define('BOT_MIDRASH', 1);
define('DEBUG', 1);

require_once(__DIR__ . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR . "loader.php");

$bot = new botMidrash();
$bot->init();
$bot->run();

