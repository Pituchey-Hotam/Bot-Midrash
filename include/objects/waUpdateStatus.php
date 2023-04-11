<?php

if (!defined('BOT_MIDRASH')) die('{"code":200}');

class waUpdateStatus {
    public $messageId;
    public $status;
    public $timestamp;
    public $from;

    public function toJson() {
        return json_encode(get_object_vars($this), true);
    }
}
