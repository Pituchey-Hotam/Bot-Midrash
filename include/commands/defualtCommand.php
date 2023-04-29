<?php

if (!defined('BOT_MIDRASH')) die('{"code":200}');

class defualtCommand extends command {
    public static function run(waUpdateMessage $update, yeshivaDetails $yeshivaDetails) {
        $text = "*ברוכים הבאים לבוט מדרש - " . $yeshivaDetails->yeshivaName . "*";

        $text .= "באמצעות הבוט תוכלו לעשות מלא דברים מגניבים!" . "\n";
        $text .= "רוצים לשמוע על זה עוד? שלחו \"עזרה\" ותקבלו מידע נוסף." . "\n" . "\n";

        $text .= "לפרטים נוספים ושאלות נוספות, מוזמנים לשלוח \"אודות\".";

        facebookApi::sendText($update->from->phoneNumber, $text, $update->messageId);
    }
}
