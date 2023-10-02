<?php

if (!defined('BOT_MIDRASH')) die('{"code":200}');

class commandDisbaleReminde extends command {
    const command = ["ביטול תזכורת לרישום", "בתל"];
    const command_message_type = avalibleWaMessagesTypes::TEXT;
    const command_type = avalibleCommandsType::EQUAL;
    const need_auth = true;

    public static function run(waUpdateMessage $update, yeshivaDetails $yeshivaDetails, user $currentUser) {
        $currentUser->shabat_registeration_mode = 0;

        $text = "❌ יותר לא תקבל תזכורות לשבת (ניתן להפעיל מחדש את התזכורות באמצעות הפקודה \"רישום לתזכורת לשבת\" (או בקצרה \"רלל\")).";

        facebookApi::sendText($update->from->phoneNumber, $text, $update->messageId);
    }
}
