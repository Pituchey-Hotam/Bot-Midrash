<?php

if (!defined('BOT_MIDRASH')) die('{"code":200}');

class user {
    public $id = -1;
    
    private static $validNames = array(
        "first_name",
        "last_name",
        "more_names",
        "category",
        "priority",
        "phone_number",
        "home_number",
        "email",
        "image_uuid",
        "shatat_name",
        "shabat_mode",
        "last_use",
        "waiting_command",
        "yeshiva_id"
    );

    public function __get($name) {
        if ($this->id < 0) {
            helpers::logErrorToFile("User not initilaize");
        }
        else {
            if (!in_array($name, user::$validNames)) {
                helpers::logErrorToFile("Try to get invalid param of user -> " . $name);
            }
            else {
                return (new db())->where("id", $this->id)->selectFirst('users', $name, [])[$name] ?? false;
            }
        }
    }

    public function __set($name, $value) {
        if ($name == "id") {
            $this->id = $value;
        }
        else if ($this->id < 0) {
            helpers::logErrorToFile("User not initilaize");
        }
        else {
            if (!in_array($name, user::$validNames)) {
                helpers::logErrorToFile("Try to set invalid param of user -> " . $name);
            }
            else {
                (new db())->where("id", $this->id)->update('users', [$name => $value]);
            }
        }
    }
}
