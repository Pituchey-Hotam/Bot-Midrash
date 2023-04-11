<?php

if (!defined('BOT_MIDRASH')) die('{"code":200}');

class parser{
    private static function parseStatus($status) {
        $res = new waUpdateStatus();
        $res->messageId = $status['id'];
        $res->timestamp = $status['timestamp'];
        $res->from = new waUpdateContact();
        $res->from->phoneNumber = $status['recipient_id'];

        if ($status['status'] == "read") {
            $res->status = avalibleWaUpdateStatuses::READ;
        }
        else if ($status['status'] == "delivered") {
            $res->status = avalibleWaUpdateStatuses::DELIVERED;
        }
        else if ($status['status'] == "sent") {
            $res->status = avalibleWaUpdateStatuses::SENT;
        }
        else {
            $res->status = avalibleWaUpdateStatuses::WAITING;
        }

        return $res;
    }

    private static function parseMessage($contact, $message) {
        $res = new waUpdateMessage();

        if ($message['type'] == "text"){
            $res = new waUpdateMessageText();
            $res->type = avalibleWaMessagesTypes::TEXT;
            $res->body = $message['text']['body'];
            $res->content = $res->body;
        }
        else if ($message['type'] == "image") {
            $res = new waUpdateMessageImage();
            $res->type = avalibleWaMessagesTypes::IMAGE;
            $res->imageId = $message['image']['id'];
            $res->caption = $message['image']['caption'];
            $res->content = $res->caption;
        }
        else if ($message['type'] == "interactive") {
            if ($message['interactive']['type'] == "button_reply") {
                $res = new waUpdateMessageButtonReply();
                $res->type = avalibleWaMessagesTypes::BUTTON_REPLY;
                $res->payload = $message['interactive']['button_reply']['id'];
                $res->text = $message['interactive']['button_reply']['title'];
                $res->content = $res->payload;
            }
            else {
                $res = new waUpdateMessageListReply();
                $res->type = avalibleWaMessagesTypes::LIST_REPLY;
                $res->payload = $message['interactive']['list_reply']['id'];
                $res->text = $message['interactive']['list_reply']['title'];
                $res->description = $message['interactive']['list_reply']['description'];
                $res->content = $res->payload;
            }
        }
        else if ($message['type'] == "button") {
            $res = new waUpdateMessageButton();
            $res->type = avalibleWaMessagesTypes::BUTTON;
            $res->payload = $message['button']['payload'];
            $res->text = $message['button']['text'];
            $res->content = $res->payload;
        }
        else {
            $res->type = avalibleWaMessagesTypes::UNKNOWN;
            $res->content = NULL;
        }

        $res->messageId = $message['id'];
        $res->timestamp = $message['timestamp'];

        $res->from = new waUpdateContact();
        $res->from->phoneNumber = $contact['wa_id'];
        $res->from->displayName = $contact['profile']['name'];

        return $res;
    }

    static function parse($json) {
        if (isset($json['entry'][0]['changes'][0]['value']['statuses'])) {
            return self::parseStatus($json['entry'][0]['changes'][0]['value']['statuses'][0]);
        }
        else if (isset($json['entry'][0]['changes'][0]['value']['messages'])) {
            return self::parseMessage($json['entry'][0]['changes'][0]['value']['contacts'][0], $json['entry'][0]['changes'][0]['value']['messages'][0]);
        }
        else {
            helpers::logErrorToFile("ERROR! parse type not found!");
        }
    }
}
