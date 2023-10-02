<?php

if (!defined('BOT_MIDRASH')) die('{"code":200}');

class commandRegisterShabat extends command {
    const command = ["רישום לשבת", "רל", "REG_TO_SHABAT", "REG_TO_SHABAT_NO"];
    const command_message_type = avalibleWaMessagesTypes::TEXT;
    const command_type = avalibleCommandsType::START_WITH;
    const need_auth = true;

    public static function run(waUpdateMessage $update, yeshivaDetails $yeshivaDetails, user $currentUser) {
        
    }
}
