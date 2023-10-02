<?php

if (!defined('BOT_MIDRASH')) die('{"code":200}');

class commandRegisterToReminde extends command {
    const command = ["רישום לתזכורת לשבת", "רלל", "תזכורת לשבת", "תל", "הרשמה לתזכורת לשבת"];
    const command_message_type = avalibleWaMessagesTypes::TEXT;
    const command_type = avalibleCommandsType::EQUAL;
    const need_auth = true;

    public static function run(waUpdateMessage $update, yeshivaDetails $yeshivaDetails, user $currentUser) {
        $currentUser->shabat_registeration_mode = 1;

        $text = "✅ נרשמת בהצלחה להודעות תזכורת לרישום לשבת. ההודעה נשלחות סביב השעה 19:24 בימים שני ושלישי." . "\n\n" .
        "(שים ♥, במידה ותרצה להסיר עצמך מהתזכורת [למה שתרצה 🤔], שלח שוב \"ביטול תזכורת לרישום\" (או בקצרה \"בתל\"))";

        facebookApi::sendText($update->from->phoneNumber, $text, $update->messageId);
    }
}
