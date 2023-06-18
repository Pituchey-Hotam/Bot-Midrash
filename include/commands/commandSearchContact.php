<?php

if (!defined('BOT_MIDRASH')) die('{"code":200}');

class commandSearchContact extends command {
    const command = ["איש קשר", "אק"];
    const command_message_type = avalibleWaMessagesTypes::TEXT;
    const command_type = avalibleCommandsType::START_WITH;
    const need_auth = true;

    public static function run(waUpdateMessage $update, yeshivaDetails $yeshivaDetails, user $currentUser) {
        $searchData = helpers::parseCommand(self::command, self::command_type, $update)[0] ?? "";

        if (mb_strlen($searchData) < 3) {
            facebookApi::sendText($update->from->phoneNumber, "אנא הזן את השם של איש הקשר שברצונך לחפש: ", $update->messageId);
            $currentUser->waiting_command = __CLASS__;
        }
        else {
            $rawContactsData = (new db())->query("SELECT * FROM `users` WHERE CONCAT(`first_name`, ' ', `more_names`, ' ', `last_name`, ' ', `more_names`) LIKE '%" . db::cleanString(str_replace(" ", "%", $searchData)) . "%' ORDER BY `users`.`priority` ASC LIMIT 0, 6", true);

            if (db::num_rows() == 0) {
                $text = "*לא נמצאו אנשי קשר.*" . "\n" . 
                "טיפ: במידה ואתם לא מצליחים למצוא איש קשר, ניתן לחפש חלקים מתוך השם." . "\n" .
                "לדוגמה, במקום משה ירחמילוביץ אפשר לכתוב, משה ירח, או ירחמילו וכו'";
                facebookApi::sendText($update->from->phoneNumber, $text, $update->messageId);
            }
            else {
                $contacts = array();

                foreach ($rawContactsData as $rawContact) {
                    $contact = array();
                    $contact['name']['formatted_name'] = $rawContact['first_name'] . " " . $rawContact['last_name'] . " - " . $rawContact['category'] . " - " . $yeshivaDetails->yeshivaName;
                    $contact['name']['first_name'] = $rawContact['first_name'];
                    $contact['name']['last_name'] = $rawContact['last_name'];
                    $contact['name']['suffix'] = " - " . $yeshivaDetails->yeshivaName;
                    $contact['org']['company'] = $yeshivaDetails->yeshivaName;
                    $contact['org']['department'] = "תלמיד מחזור " . $rawContact['category'];
                    
                    if(!empty($rawContact['phone_number'])){
                        $contact['phones'][] = array(
                            "phone" => "+" . $rawContact['phone_number'],
                            "wa_id" => $rawContact['phone_number'],
                            "type" => "CELL",
                        );
                    }
                    
                    if(!empty($rawContact['home_number'])){
                        $contact['phones'][] = array(
                            "phone" => "+972" . $rawContact['home_number'],
                            "type" => "HOME",
                        );
                    }
                    
                    if(!empty($rawContact['email'])){
                        $contact['emails'][] = array(
                            "email" => $rawContact['email'],
                            "type" => "PREF"
                        );
                    }
                    
                    $contacts[] = $contact;
                }

                if(count($contacts) > 5) {
                    array_pop($contacts);
                    $text = "💡 בעת חיפוש אנשי קשר, רק חמשת אנשי הקשר הראשונים המתאימים לשאילתה נשלחים." . "\n" . "לא מוצאים את מי שאתם מחפשים? נסו לחפש באופן יותר ממוקד.";
                    facebookApi::sendText($update->from->phoneNumber, $text, $update->messageId);
                }

                facebookApi::sendContacts($update->from->phoneNumber, $contacts, $update->messageId);
            }
        }
    }
}
