<?php

if (!defined('BOT_MIDRASH')) die('{"code":200}');

class waUpdateMessage {
    public $messageId;
    public $from;
    public $timestamp;
    public $type;
    public $content;

    public function toJson() {
        return json_encode(get_object_vars($this), true);
    }
}
