<?php

if (!defined('BOT_MIDRASH')) die('{"code":200}');

class commandDayTimes extends command {
    const command = ["זמני היום", "זה"];
    const command_message_type = avalibleWaMessagesTypes::TEXT;
    const command_type = avalibleCommandsType::EQUAL;
    const need_auth = false;

    public static function run(waUpdateMessage $update) {
        var_dump(facebookApi::sendText($update->from->phoneNumber, "Hi!", $update->messageId));
    }
}
