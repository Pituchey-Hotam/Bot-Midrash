<?php

if (!defined('BOT_MIDRASH')) die('{"code":200}');

class defualtCommand extends command {
    public static function run(waUpdateMessage $update) {
        facebookApi::sendText($update->from->phoneNumber, "Defualt Text :)", $update->messageId);
    }
}
