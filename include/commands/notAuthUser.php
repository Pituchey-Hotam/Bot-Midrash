<?php

if (!defined('BOT_MIDRASH')) die('{"code":200}');

class notAuthUser extends command {
    public static function run(waUpdateMessage $update, yeshivaDetails $yeshivaDetails, user $currentUser) {
        $text = "⛔ *משתמש יקר, אינך מזוהה במערכת. לצורך רישום למערכת יש לפנות לאחראי הישיבתי.*";

        facebookApi::sendText($update->from->phoneNumber, $text, $update->messageId);
    }
}
