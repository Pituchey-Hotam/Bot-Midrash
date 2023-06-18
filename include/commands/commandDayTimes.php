<?php

if (!defined('BOT_MIDRASH')) die('{"code":200}');

class commandDayTimes extends command {
    const command = ["זמני היום", "זה"];
    const command_message_type = avalibleWaMessagesTypes::TEXT;
    const command_type = avalibleCommandsType::EQUAL;
    const need_auth = false;

    public static function run(waUpdateMessage $update, yeshivaDetails $yeshivaDetails, user $currentUser) {
        $sunInfo = date_sun_info(time(), 31.771959, 35.217018);
        $zmanitMga  = ((($sunInfo['sunset'] + (72 * 60)) - ($sunInfo['sunrise'] - (72 * 60))) / 12) / 60;
        $zmanitGra  = (($sunInfo['sunset'] - $sunInfo['sunrise']) / 12) / 60;

        function tsToTime($timestamp){return date("H:i", $timestamp);}

        $text = "*זמני היום לעיר מעלה אדומים:*" . "\n\n" . 
              "*🔴 הערה חשובה: אין לסמוך על זמנים אלו לדאורייתא. תמיד לקחת טווח ביטחון!*" . "\n\n" .
              "*תאריך עברי:* " . iconv('WINDOWS-1255', 'UTF-8', jdtojewish(unixtojd(), true, CAL_JEWISH_ADD_GERESHAYIM)) . "\n" .
              "*עלות השחר:* " . tsToTime($sunInfo['sunrise'] - (72 * 60)) . "\n" . 
              "*זמן טלית ותפילין:* " . tsToTime($sunInfo['sunrise'] - (45 * 60)) . "\n" . 
              "*זריחה:* " . tsToTime($sunInfo['sunrise']) . "\n" . 
              "*סוף זמן ק\"ש  (מג\"א):* " . tsToTime(($sunInfo['sunrise'] - (72 * 60)) + ($zmanitMga * (60 * 3))) . "\n" . 
              "*סוף זמן תפילה (מג\"א):* " . tsToTime(($sunInfo['sunrise'] - (72 * 60)) + ($zmanitMga * (60 * 4))) . "\n" . 
              "*סוף זמן ק\"ש (גר\"א):* " . tsToTime($sunInfo['sunrise'] + $zmanitGra * (60 * 3)) . "\n" . 
              "*סוף זמן תפילה (גר\"א):* " . tsToTime($sunInfo['sunrise'] + $zmanitGra * (60 * 4)) . "\n" . 
              "*חצות היום:* " . tsToTime($sunInfo['sunrise'] + $zmanitGra * (60 * 6)) . "\n" . 
              "*מנחה גדולה:* " . tsToTime($sunInfo['sunrise'] + $zmanitGra * (60 * 6.5)) . "\n" . 
              "*פלג המנחה:* " . tsToTime($sunInfo['sunset'] - $zmanitGra * (60 * 1.25)) . "\n" . 
              "*שקיעה:* " . tsToTime($sunInfo['sunset']) . "\n" . 
              "*צאת הכוכבים:* " . tsToTime($sunInfo['sunset'] + (20 * 60));

        facebookApi::sendText($update->from->phoneNumber, $text, $update->messageId);
    }
}
