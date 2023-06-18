<?php

if (!defined('BOT_MIDRASH')) die('{"code":200}');

class commandSearchLesson extends command {
    const command = ["שיעור", "ש"];
    const command_message_type = avalibleWaMessagesTypes::TEXT;
    const command_type = avalibleCommandsType::START_WITH;
    const need_auth = false;

    public static function run(waUpdateMessage $update, yeshivaDetails $yeshivaDetails, user $currentUser) {
        $searchData = helpers::parseCommand(self::command, self::command_type, $update)[0] ?? "";

        if (mb_strlen($searchData) < 3) {
            facebookApi::sendText($update->from->phoneNumber, "אנא הזן את שם השיעור שברצונך לחפש: ", $update->messageId);
            $currentUser->waiting_command = __CLASS__;
        }
        else {
            $BaseUrl = "https://www.ybm.org.il/api/LessonsList/LessonsList";
            $data = array("Rabi" => array("Id" => 0), "SearchTxt" => $searchData, "CatId" => 0, "Page" => 1, "OrderType" => 1);
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $BaseUrl);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("accept: application/json, text/plain, */*", "content-type: application/json;charset=UTF-8"));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch ,CURLOPT_POSTFIELDS, json_encode($data));

            if (DEBUG) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            }
            
            $resLessons = json_decode(curl_exec($ch), true);
            curl_close($ch);

            if($resLessons == null || !isset($resLessons['Lessons'])){
                facebookApi::sendText($update->from->phoneNumber, "*לא נמצאו שיעורים*", $update->messageId);
            }
            else{
                $interactiveData = array();
                $interactiveData['type'] = "list";
                
                $header = $searchData;
                if (mb_strlen($header) > 16)
                    $header = mb_substr($header, 0, 13) . '...';
                
                $interactiveData['header'] = array(
                    "type" => "text",
                    "text" => "5 תוצאות החיפוש הראשונות ל-\"" . $header . "\" מאתר הישיבה"
                );
                
                $interactiveData['action']['button'] = "לחץ לרשימת השיעורים";
                $interactiveData['action']['sections'][0]['title'] = "שיעורים";
                $interactiveData['action']['sections'][0]['rows'] = array();
                
                $i = 0;
                $text = "";
                
                foreach ($resLessons['Lessons'] as $lesson){
                    if($i == 5) break;
                    
                    $text .= "שיעור מספר " . ($i+1) . "\n" .
                            "*נושא:* {1}" . "\n" .
                            ($lesson['LessonLength'] == 0 ? "" : "*אורך:* {2}" . "\n") .
                            "*מעביר:* {3}" . "\n" .
                            (empty(trim($lesson['HebrewDate'])) ? "" : "*תאריך:* {4}" . "\n") .
                            "*מתוך הסדרה:* {5}" . "\n" .
                            "\n";
                    
                    $text = str_replace("{1}", trim($lesson['LessonName']), $text);
                    $text = str_replace("{2}", floor($lesson['LessonLength'] / 60) . "." . ($lesson['LessonLength'] % 60), $text);
                    $text = str_replace("{3}", trim($lesson['RabiName']), $text);
                    $text = str_replace("{4}", trim($lesson['HebrewDate']), $text);
                    $text = str_replace("{5}", trim($lesson['SidraName']), $text);
    
                    $description = trim($lesson['LessonName']);
                    if (mb_strlen($description) > 72)
                        $description = mb_substr($description, 0, 68) . '...';
    
                    $interactiveData['action']['sections'][0]['rows'][] = array("id" => "LessonId\n" . $lesson['Id'] . "\n" . uniqid(), "title" => "שיעור " . ($i + 1), "description" => $description);

                    $i++;
                }
                
                $interactiveData['body']['text'] = trim($text);

                facebookApi::sendInteractive($update->from->phoneNumber, $interactiveData, $update->messageId);
            }
        }
    }
}
