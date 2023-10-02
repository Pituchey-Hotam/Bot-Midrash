<?php

if (!defined('BOT_MIDRASH')) die('{"code":200}');

date_default_timezone_set("Asia/Jerusalem");

define('WEBHOOK_TOKEN', 'JY8B7Wz$Ve2^sv2d**c2Wc2Ag^Z7MZ8#%opR8&jaEL*n$k^x593!!jM^');
define('PHONE_ID', "105726442179368");
define('ACCESS_TOKEN', file_get_contents("token.txt"));

define('BOT_MIDRASH_DIR', dirname(__DIR__));
define('BOT_MIDRASH_INCLUDE_DIR', BOT_MIDRASH_DIR . DIRECTORY_SEPARATOR . "include");
define('BOT_MIDRASH_IMAGES_DIR', BOT_MIDRASH_DIR . DIRECTORY_SEPARATOR . "images");

define('DATABASE_CONFIG',
    array(
        "host" => "localhost",
        "username" => "root",
        "password" => "",
        "dbname" => "BotMidrashDB"
    )
);

define('SHABAT_REGISTRATION_OPTIONS',
    array(
        "כן" => 1,
        "לא" => 0,
        "אולי" => 0.5,
        "2" => 2,
        "3" => 3,
        "4" => 4
    )
);


