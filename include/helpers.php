<?php

if (!defined('BOT_MIDRASH')) die('{"code":200}');

class helpers {
    public static function isShabat(){
        $sunInfo = date_sun_info(time(), 31.771959, 35.217018);
        
        if((date("l") == "Friday" && time() > $sunInfo['sunrise']))
            return true;
        if((date("l") == "Saturday" && time() < $sunInfo['subset']))
            return true;
        else
            return false;
    }

    public static function logErrorToFile($message) {
        $callFunc0 = debug_backtrace()[0];
        $callFunc1 = debug_backtrace()[1];

        file_put_contents(
            "error.log", 
            $callFunc0["file"] . ":" . $callFunc0["line"] . ":" . $callFunc1["class"] . ":" . $callFunc1["function"] . ": " . $message . "\n", 
            FILE_APPEND
        );
        
        die("ERROR!");
    }

    public static function checkIfRunThisCommand(array $commandWords, $commandType, waUpdateMessage $update) {
        $firstLine = explode("\n", $update->content)[0];

        if ($commandType == avalibleCommandsType::EQUAL) {
            foreach ($commandWords as $word) {
                if ($firstLine == $word) {
                    return true;
                }
            }
        }
        else if ($commandType == avalibleCommandsType::CONTAIN) {
            foreach ($commandWords as $word) {
                if (strpos($firstLine, $word) !== false) {
                    return true;
                }
            }
        }
        else if ($commandType == avalibleCommandsType::START_WITH) {
            foreach ($commandWords as $word) {
                if (mb_substr($firstLine, 0, mb_strlen($word) + 1) == $word . " ") {
                    return true;
                }
            }
        }
        
        return false;
    }
}